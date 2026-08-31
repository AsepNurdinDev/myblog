<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Technology', 'slug' => 'technology'],
            ['name' => 'Programming', 'slug' => 'programming'],
            ['name' => 'Tutorial', 'slug' => 'tutorial'],
            ['name' => 'Journey', 'slug' => 'journey'],
            ['name' => 'Life', 'slug' => 'life'],
            ['name' => 'Thoughts', 'slug' => 'thoughts'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name']]
            );
        }
    }
}
