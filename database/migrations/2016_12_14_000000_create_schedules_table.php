<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    private string $table_name = 'schedules';

    public function up(): void
    {
        if (Schema::hasTable($this->table_name)) { return; }
        
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('command');
            $table->string('arguments')->nullable();
            $table->string('frequency');
            $table->string('ping_before')->nullable();
            $table->string('ping_after')->nullable();
            $table->string('author_id');
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
