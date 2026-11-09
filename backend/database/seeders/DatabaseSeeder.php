<?php

namespace Database\Seeders;

use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            CategorySeeder::class,
            SkillSeeder::class,
            TagSeeder::class,
        ]);

        $this->syncAdminSkills();
    }

    /**
     * Attach the admin's mastered skills to their profile.
     *
     * Uses sync() so this is safe to run repeatedly without
     * creating duplicate pivot rows.
     */
    protected function syncAdminSkills(): void
    {
        $profile = User::where('email', 'admin@myblog.test')->first()?->profile;

        if (! $profile) {
            return;
        }

        $skillIds = Skill::whereIn('slug', [
            'laravel',
            'php',
            'go',
            'javascript',
            'typescript',
            'docker',
            'linux',
            'mysql',
            'postgresql',
            'git',
        ])->pluck('id');

        $profile->skills()->sync($skillIds);
    }
}
