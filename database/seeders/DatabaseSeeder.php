<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * la manera mas simple de poblar la tabla que del DatabaseSeeder.php de Authors
 */

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call('UsersTableSeeder');
        //factory(Book::class, 30)->create();
        Book::factory()->count(30)->create();
    }
}
