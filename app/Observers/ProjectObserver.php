<?php

namespace App\Observers;

use App\Models\Project;
use App\Models\Step;

class ProjectObserver
{
    /**
     * Handle the Project "created" event.
     */
    public function created(Project $project): void
    {
        Step::create([
            'project_id' => $project->id,
        ]);
    }

    /**
     * Handle the Project "updated" event.
     */
    public function updated(Project $project): void
    {
        //
    }

    /**
     * Handle the Project "deleted" event.
     */
    public function deleted(Project $project): void
    {
        //
    }

    /**
     * Handle the Project "restored" event.
     */
    public function restored(Project $project): void
    {
        //
    }

    /**
     * Handle the Project "force deleted" event.
     */
    public function forceDeleted(Project $project): void
    {
        //
    }
}
