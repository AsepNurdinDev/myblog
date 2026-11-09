<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the "About" page.
     *
     * This is a single-owner blog, so there is only ever one Profile
     * (belonging to the admin User). We simply fetch it rather than
     * scoping by a route parameter.
     */
    public function show(): View
    {
        $profile = Profile::query()
            ->with(['user', 'skills'])
            ->firstOrFail();

        return view('profile.show', [
            'profile' => $profile,
        ]);
    }
}
