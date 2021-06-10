<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attributable_id');
            $table->string('attributable_type')->index();
            $table->string('attribute_type_code_name');
            $table->foreign('attribute_type_code_name')
                  ->references('code_name')
                  ->on('attribute_types')
                  ->onDelete('cascade');
            $table->unsignedBigInteger('value_id');
            $table->enum('value_type', array_keys(config('logos.valueTypes')))->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attributes');
    }
}
