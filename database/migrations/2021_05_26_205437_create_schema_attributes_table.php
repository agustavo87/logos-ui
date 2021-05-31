<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchemaAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schema_attributes', function (Blueprint $table) {
            $table->unsignedBigInteger('schema_id');
            $table->string('base_attribute_code_name', 50);
            $table->primary(['schema_id', 'base_attribute_code_name']);
            $table->foreign('schema_id')
                ->references('id')
                ->on('schemas');
            $table->foreign('base_attribute_code_name')
                ->references('code_name')
                ->on('base_attributes');

            $table->string('code_name', 50)->index()->unique();
            $table->string('label', 100)->nullable();
            $table->tinyInteger('order');

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
        Schema::dropIfExists('schema_attributes');
    }
}
