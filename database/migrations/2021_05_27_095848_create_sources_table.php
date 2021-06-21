<?php

use Arete\Logos\Application\Ports\Interfaces\LogosEnviroment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CreateSourcesTable extends Migration
{
    protected LogosEnviroment $logos;

    public function __construct()
    {
        $this->logos = app(LogosEnviroment::class);
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sources', function (Blueprint $table) {

            $users = $this->logos->getUsersTableData();

            $table->id();
            $table->timestamps();

            $table->unsignedBigInteger($users->FK);
            $table->foreign($users->FK)
                  ->references($users->PK)
                  ->on($users->table)
                  ->onDelete('cascade');

            $table->string('source_type_code_name', 50);
            $table->foreign('source_type_code_name')
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
