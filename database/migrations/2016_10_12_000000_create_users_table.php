<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{

    private string $table_name = 'users';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {

        if (Schema::hasTable($this->table_name)) { return; }

        Schema::create($this->table_name, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('username')->unique();
            $table->string('slug')->nullable();
            $table->string('password');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->integer('role_id')->default(2);
            $table->integer('visits')->default(0);
            $table->string('image')->nullable();
            $table->rememberToken()->nullable();
            $table->timestamps();
            $table->string('api_token')->unique()->nullable();
            $table->boolean('active')->default(false);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::drop($this->table_name);
    }

}