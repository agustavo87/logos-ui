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
            $table->timestamps();
            $table->unsignedBigInteger('attributable_id');
            $table->string('attributable_type')->index();
            $table->string('base_attribute_code_name');
            $table->foreign('base_attribute_code_name')
                  ->references('code_name')
                  ->on('base_attribute')
                  ->onDelete('cascade');
            $table->unsignedBigInteger('value_id');
            $table->enum('value_type', config('logos.valueTypes'))->index();
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
