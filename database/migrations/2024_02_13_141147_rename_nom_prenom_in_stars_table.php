<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('stars', function (Blueprint $table) {
            $table->renameColumn('nom', 'name');
            $table->renameColumn('prenom', 'first_name');
        });
    }

    public function down()
    {
        Schema::table('stars', function (Blueprint $table) {
            $table->renameColumn('name', 'nom');
            $table->renameColumn('first_name', 'prenom');
        });
    }

};
