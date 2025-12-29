<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHeaderImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    private string $table_name = 'header_images';
    
    public function up(): void
    {
        if (Schema::hasTable($this->table_name)) { return; }

        Schema::create($this->table_name, function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->string('type')->nullable();
            $table->string('link')->nullable();
            $table->string('language')->default('en');
            $table->text('description')->nullable();
            $table->string('image');
            $table->integer('order')->nullable();
            $table->integer('author_id');
            $table->timestamps();
            $table->boolean('active')->default(true);
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
