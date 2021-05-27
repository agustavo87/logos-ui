<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParticipationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('participations', function (Blueprint $table) {
            $table->unsignedBigInteger('creator_id');
            $table->unsignedBigInteger('source_id');
            $table->primary(['creator_id', 'source_id']);
            $table->foreign('source_id')
                  ->references('id')
                  ->on('sources')
                  ->onDelete('cascade');
            $table->foreign('creator_id')
                  ->references('id')
                  ->on('creators')
                  ->onDelete('cascade');
            $table->tinyInteger('relevance', false, true);
            $table->string('role_code_name', 50);
            $table->foreign('role_code_name')
                  ->references('code_name')
                  ->on('roles')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('participations');
    }
}
