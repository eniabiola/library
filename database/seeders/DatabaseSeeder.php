<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Publisher;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(TableSeeder::class);
/*      //This works for local production
         \App\Models\User::factory()->count(10)->create();
        \App\Models\Book::factory()->count(120)->create();
        \App\Models\Author::factory()->count(40)->create();
       // Get all the books attaching up to 3 random authors to each book
        $authors = \App\Models\Author::all();

        // Populate the pivot table
        \App\Models\Book::all()->each(function ($book) use ($authors) {
            $book->authors()->attach(
                $authors->random(rand(1, 3))->pluck('id')->toArray()
            );
        });*/


    }
}
