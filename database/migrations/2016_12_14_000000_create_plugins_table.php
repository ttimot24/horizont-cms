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
            $table->string('version')->nullable()->comment('Installed version');
            $table->integer('area')->nullable()->comment('Main widget area');
            $table->integer('permission')->nullable()->comment('Plugin maintained permissions');
            $table->string('tables')->nullable()->comment('Plugin maintained tables');
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
