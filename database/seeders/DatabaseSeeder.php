<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'nico@deblauwe.be',
        ]);
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        User::factory(15)->create();

        $articles = Article::factory(20)->create();
        Comment::factory(35)->create();
        Comment::factory(5)->unapproved()->create();

        Category::factory(5)->create();
        foreach ($articles as $article) {
            $subset = Category::inRandomOrder()->take(rand(0, 5))->pluck('id')->toArray();
            $article->categories()->attach($subset);
        }

    }
}
