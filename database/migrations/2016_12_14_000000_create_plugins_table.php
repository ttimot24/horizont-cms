<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePluginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    private string $table_name = 'plugins';

    public function up(): void
    {
        if (Schema::hasTable($this->table_name)) { return; }
        
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->increments('id');
            $table->string('root_dir')->unique()->comment('Plugin context');
            $table->integer('area')->nullable();
            $table->integer('permission')->nullable();
            $table->string('tables')->nullable();
            $table->timestamps();
            $table->integer('active');
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
