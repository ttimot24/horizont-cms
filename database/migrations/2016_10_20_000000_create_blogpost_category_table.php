<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlogpostCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    private string $table_name = 'blogpost_categories';
    
    public function up(): void
    {
        if (Schema::hasTable($this->table_name)) { return; }

        Schema::create($this->table_name, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('language')->default('en');
            $table->integer('author_id');
            $table->string('image')->nullable();
            $table->timestamps();
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
