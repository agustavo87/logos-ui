<?php

use Arete\Logos\Application\Ports\Interfaces\LogosEnviroment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreatorsTable extends Migration
{

    protected LogosEnviroment $logos;

    public function __construct() {
        $this->logos = app(LogosEnviroment::class);
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('creators', function (Blueprint $table) {
            $users = $this->logos->getOwnersTableData();

            $table->id();
            $table->unsignedBigInteger($users->FK);
            $table->timestamps();
            $table->string('creator_type_code_name', 50);
            $table->foreign('creator_type_code_name')
                  ->references('code_name')
                  ->on('creator_types')
                  ->onDelete('cascade');

            $table->foreign($users->FK)
                  ->references($users->PK)
                  ->on($users->table)
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
        Schema::dropIfExists('creators');
    }
}
