<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sources', function (Blueprint $table) {

            $usersTable = config('usersTable', 'users');
            $usersPK = config('usersPK', 'id');
            $usersFK = "{$usersTable}_{$usersPK}";

            $table->id();
            $table->timestamps();

            $table->unsignedBigInteger($usersFK);
            $table->foreign($usersFK)
                  ->references($usersPK)
                  ->on($usersTable)
                  ->onDelete('cascade');

            $table->string('surce_type_code_name', 50);
            $table->foreign('surce_type_code_name')
                  ->references('code_name')
                  ->on('source_types')
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
        Schema::dropIfExists('sources');
    }
}
