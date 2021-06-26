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
            $table->unsignedBigInteger('attributable_id');
            $table->string('attributable_genus');
            $table->string('attribute_type_code_name');
            $table->foreign('attribute_type_code_name')
                  ->references('code_name')
                  ->on('attribute_types')
                  ->onDelete('cascade');
            $table->json('complex_value')->nullable();
            $table->string('text_value')->nullable();
            $table->integer('number_value')->nullable();
            $table->dateTime('date_value')->nullable();
            $table->unique(
                ['attributable_id', 'attributable_genus', 'attribute_type_code_name'],
                'attributable_attribute_key'
            );
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
