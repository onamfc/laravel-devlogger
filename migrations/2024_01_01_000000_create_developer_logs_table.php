<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableName = config('devlogger.table_name', 'developer_logs');
        $connection = config('devlogger.database_connection');

        Schema::connection($connection)->create($tableName, function (Blueprint $table) {
            $table->id();
            $table->string('level')->index();
            $table->string('queue')->nullable()->index();
            $table->longText('log');
            $table->json('context')->nullable();
            $table->string('file_path')->nullable();
            $table->integer('line_number')->nullable();
            $table->string('exception_class')->nullable()->index();
            $table->longText('stack_trace')->nullable();
            $table->string('request_url')->nullable();
            $table->string('request_method', 10)->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('status')->default('open')->index();
            $table->json('tags')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Add indexes for better performance
            $table->index(['level', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableName = config('devlogger.table_name', 'developer_logs');
        $connection = config('devlogger.database_connection');

        Schema::connection($connection)->dropIfExists($tableName);
    }
};