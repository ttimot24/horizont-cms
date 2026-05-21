<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    private string $table_name = 'users';

    /**
     * Run the migrations.
     */
    public function up(): void
    {

        if (Schema::hasTable($this->table_name)) {
            return;
        }

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
     */
    public function down(): void
    {
        Schema::drop($this->table_name);
    }
}
