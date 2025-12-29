<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlogpostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    private string $table_name = 'blogposts';

    public function up(): void
    {
        if (Schema::hasTable($this->table_name)) { return; }
        
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('slug');
            $table->string('language')->default('en');
            $table->string('summary')->nullable();
            $table->text('text')->nullable();
            $table->integer('comments_enabled');
            $table->integer('author_id');
            $table->string('image')->nullable();
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
