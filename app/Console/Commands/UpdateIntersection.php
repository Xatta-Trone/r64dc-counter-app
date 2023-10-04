<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;
use App\Models\ProjectIntersection;
use Illuminate\Database\Eloquent\Collection;

class UpdateIntersection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-intersection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Project::whereNotNull('parent_project_id')
            ->chunkById(100, function (Collection $projects) {

                foreach ($projects as $project) {
                    if ($project->project_intersection_id == null) {
                        // create intersection
                        $check = ProjectIntersection::where('parent_project_id', $project->parent_project_id)->where('intersection_name', $project->intersection)->first();
                        if ($check) {
                            $project->update(['project_intersection_id' => $check->id]);
                        } else {
                            $intersection = ProjectIntersection::create(['parent_project_id' => $project->parent_project_id, 'intersection_name' => $project->intersection]);
                            $project->update(['project_intersection_id' => $intersection->id]);
                        }
                    }
                }
            }, $column = 'id');
    }
}
