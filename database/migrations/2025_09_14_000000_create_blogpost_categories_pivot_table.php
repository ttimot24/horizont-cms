<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    private string $table_name = 'blogpost_categories_pivot';

    public function up(): void
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('blogpost_id');
            $table->unsignedInteger('blogpost_category_id');
            
            $table->foreign('blogpost_id', 'fk_blogpost_pivot')
                  ->references('id')
                  ->on('blogposts')
                  ->onDelete('cascade');

            $table->foreign('blogpost_category_id', 'fk_blogpost_category_pivot')
                  ->references('id')
                  ->on('blogpost_categories')
                  ->onDelete('cascade');

            $table->timestamps();

            $table->unique(['blogpost_id', 'blogpost_category_id'], 'pivot_blogpost_category_unique');

        });


        DB::table('blogposts')
            ->whereNotNull('category_id')
            ->select('id', 'category_id')
            ->orderBy('id')
            ->chunk(500, function ($posts) {
                DB::table($this->table_name)->insert(
                    $posts->map(fn ($post) => [
                        'blogpost_id'          => $post->id,
                        'blogpost_category_id' => $post->category_id,
                        'created_at'           => now(),
                        'updated_at'           => now(),
                    ])->toArray()
                );
        });

    }

    public function down(): void
    {
        Schema::dropIfExists($this->table_name);
    }
};
