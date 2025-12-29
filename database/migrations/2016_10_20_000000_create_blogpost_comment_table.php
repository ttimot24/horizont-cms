<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlogpostCommentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    private string $table_name = 'blogpost_comments';

    public function up(): void
    {
        if (Schema::hasTable($this->table_name)) { return; }

        Schema::create($this->table_name, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('blogpost_id');
            $table->integer('author_id');
            $table->text('comment');
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
