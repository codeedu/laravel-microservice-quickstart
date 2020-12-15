<?php

use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = \App\Models\Category::all();
        factory(\App\Models\Genre::class,10)->create()
            ->each(function(\App\Models\Genre $genre) use($categories){
               $categoriesId = $categories->random(5)->pluck('id')->toArray();
               $genre->categories()->attach($categoriesId);
            });
    }
}
