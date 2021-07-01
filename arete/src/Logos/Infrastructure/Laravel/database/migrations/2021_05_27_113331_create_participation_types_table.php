<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParticipationTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('participation_types', function (Blueprint $table) {
            $table->string('source_type_code_name', 50);
            $table->string('role_code_name', 50);
            $table->primary(['source_type_code_name', 'role_code_name']);
            $table->foreign('source_type_code_name')
                  ->references('code_name')
                  ->on('source_types')
                  ->onDelete('cascade');
            $table->foreign('role_code_name')
                  ->references('code_name')
                  ->on('roles')
                  ->onDelete('cascade');
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
