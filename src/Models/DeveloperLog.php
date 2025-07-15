<?php

namespace DevLoggerPackage\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;

class DeveloperLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'level',
        'queue',
        'log',
        'context',
        'file_path',
        'line_number',
        'status',
        'tags',
        'updated_by',
        'exception_class',
        'stack_trace',
        'request_url',
        'request_method',
        'user_id',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'context' => 'array',
        'tags' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
        $this->table = config('devlogger.table_name', 'developer_logs');
        $this->connection = config('devlogger.database_connection');
    }

    /**
     * Scope for open logs
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope for closed logs
     */
    public function scopeClosed(Builder $query): Builder
    {
        return $query->where('status', 'closed');
    }

    /**
     * Scope for specific log level
     */
    public function scopeLevel(Builder $query, string $level): Builder
    {
        return $query->where('level', $level);
    }

    /**
     * Scope for logs within date range
     */
    public function scopeDateRange(Builder $query, Carbon $from, Carbon $to): Builder
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    /**
     * Scope for logs by queue
     */
    public function scopeQueue(Builder $query, string $queue): Builder
    {
        return $query->where('queue', $queue);
    }

    /**
     * Mark log as closed
     */
    public function markAsClosed(?int $userId = null): bool
    {
        return $this->update([
            'status' => 'closed',
            'updated_by' => $userId,
        ]);
    }

    /**
     * Mark log as open
     */
    public function markAsOpen(): bool
    {
        return $this->update([
            'status' => 'open',
            'updated_by' => null,
        ]);
    }

    /**
     * Add tags to the log
     */
    public function addTags(array $tags): bool
    {
        $existingTags = $this->tags ?? [];
        $newTags = array_unique(array_merge($existingTags, $tags));
        
        return $this->update(['tags' => $newTags]);
    }

    /**
     * Remove tags from the log
     */
    public function removeTags(array $tags): bool
    {
        $existingTags = $this->tags ?? [];
        $newTags = array_diff($existingTags, $tags);
        
        return $this->update(['tags' => array_values($newTags)]);
    }

    /**
     * Get formatted context for display
     * 
     * @return Attribute
     */
    protected function formattedContext(): Attribute
    {
        return Attribute::make(
            get: fn () => empty($this->context) 
                ? '' 
                : json_encode($this->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    /**
     * Get short file path (relative to project root)
     * 
     * @return Attribute
     */
    protected function shortFilePath(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (empty($this->file_path)) {
                    return 'unknown';
                }
                
                $basePath = base_path();
                return str_replace($basePath . '/', '', $this->file_path);
            }
        );
    }
}