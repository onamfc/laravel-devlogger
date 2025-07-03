<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeveloperLogsTable extends Migration {
    public function up() {
        Schema::create( 'developer_logs', function ( Blueprint $table ) {
            $table->id();
            $table->string( 'queue' )->nullable();
            $table->string( 'level' )->nullable();
            $table->longText( 'log' )->nullable();
            $table->json( 'context' )->nullable();
            $table->string( 'file_path' )->nullable();
            $table->string( 'status' )->default( 'open' )->nullable();
            $table->json( 'tags' )->nullable();
            $table->unsignedBigInteger( 'updated_by' )->nullable();
            $table->softDeletes();
            $table->timestamps();
        } );
    }

    public function down() {
        Schema::dropIfExists( 'developer_logs' );
    }
}
