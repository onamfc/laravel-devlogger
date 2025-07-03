<?php

namespace DevLoggerPackage\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeveloperLog extends Model {
    use HasFactory, SoftDeletes;

    protected $table = 'developer_logs';

    protected $fillable = [
        'level',
        'queue',
        'log',
        'context',
        'file_path',
        'status',
        'tags',
        'updated_by',
    ];

    protected $casts = [
        'context' => 'array',
        'tags'    => 'array',
    ];

    public function openLogs() {
        return $this->where( 'status', 'open' )->get();
    }

    public function closeLogs() {
        return $this->where( 'status', 'closed' )->get();
    }
}
