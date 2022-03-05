<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        User::factory()->create([
            "name" => "Cursosdesarrolloweb",
            "email" => "laravel9@cursosdesarrolloweb.es",
        ]);
        $this->call(CategorySeeder::class);
        Article::factory(20)->create();
    }
}
