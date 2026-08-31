<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * Display a paginated list of projects.
     *
     * NOTE: ProjectStatus only defines Ongoing, Completed and Archived —
     * there is no "hidden"/"draft" case in the enum, so the Project model
     * has no concept of a private project. Every project is public by
     * design. If you want some projects to stay unlisted, that requires
     * adding a case to ProjectStatus (or a separate visibility flag) first.
     */
    public function index(): View
    {
        $projects = Project::query()
            ->with('skills')
            ->latest('started_at')
            ->paginate(9);

        return view('projects.index', [
            'projects' => $projects,
        ]);
    }

    /**
     * Display a single project.
     */
    public function show(Project $project): View
    {
        $project->load('skills');

        return view('projects.show', [
            'project' => $project,
        ]);
    }
}
