<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@myblog.test'],
            [
                'name' => 'Asep Nurdin',
                'password' => Hash::make('password'),
            ]
        );

        Profile::updateOrCreate(
            ['user_id' => $admin->id],
            [
                'headline' => 'Software Engineer & Programmer',
                'bio' => 'Menulis tentang teknologi, programming, dan hal-hal yang saya pelajari sehari-hari.',
                'location' => 'Indonesia',
                'avatar' => null,
                'website' => null,
                'github' => null,
                'linkedin' => null,
                'instagram' => null,
                'twitter' => null,
            ]
        );
    }
}
