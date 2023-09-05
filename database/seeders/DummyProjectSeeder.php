<?php

namespace Database\Seeders;

use Exception;
use App\Models\User;
use App\Models\Project;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DummyProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Sharif Ahmed',
            'email' => 'sharifahmedrafat2@gmail.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);
        // create project

        $c = CarbonPeriod::since("00:00")->minutes(5)->until("23:59")->toArray();


        $projectData = [];
        $times = [];

        foreach ($c as $a) {
            $times[] = $a->format('H:i');
        }
        $times[] = "24:00";



        $items = [
            [
                "id" => time() . "1",
                "key" => "e",
                "left" => rand(0, 100),
                "right" =>  rand(0, 100),
                "through" =>  rand(0, 100),
                "title" => "e"
            ],
            [
                "id" => time() . "2",
                "key" => "d",
                "left" => rand(0, 100),
                "right" =>  rand(0, 100),
                "through" =>  rand(0, 100),
                "title" => "d"
            ],
            [
                "id" => time() . "3",
                "key" => "a",
                "left" => rand(0, 100),
                "right" =>  rand(0, 100),
                "through" =>  rand(0, 100),
                "title" => "a"
            ],

            [
                "id" => time() . "4",
                "key" => "b",
                "left" => rand(0, 100),
                "right" =>  rand(0, 100),
                "through" =>  rand(0, 100),
                "title" => "b"
            ],

            [
                "id" => time() . "5",
                "key" => "f",
                "left" => rand(0, 100),
                "right" =>  rand(0, 100),
                "through" =>  rand(0, 100),
                "title" => "f"
            ],

            [
                "id" => time() . "6",
                "key" => "g",
                "left" => rand(0, 100),
                "right" =>  rand(0, 100),
                "through" =>  rand(0, 100),
                "title" => "g"
            ],
            [
                "id" => time() . "7",
                "key" => "h",
                "left" => rand(0, 100),
                "right" =>  rand(0, 100),
                "through" =>  rand(0, 100),
                "title" => "h"
            ],

            [
                "id" => time() . "8",
                "key" => "i",
                "left" => rand(0, 100),
                "right" =>  rand(0, 100),
                "through" =>  rand(0, 100),
                "title" => "i"
            ],

            [
                "id" => time() . "9",
                "key" => "j",
                "left" => rand(0, 100),
                "right" =>  rand(0, 100),
                "through" =>  rand(0, 100),
                "title" => "j"
            ],


        ];



        for ($i = 0; $i < count($times); $i++) {
            if ($i == count($times) - 1) {
                break;
            }

            $projectData[] = [
                'start_time' => $times[$i],
                'end_time' => $times[$i + 1],
                'data' => $items,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }


        try {

            $data = [
                'title' => "Test data",
                'day' => Carbon::now(),
                'intersection' => "test_intersection",
                'approach_name' => "test approach",
                'weather_condition' => "sunny",
                'user_id' => $user->id,
            ];


            DB::transaction(function () use ($projectData, $data) {
                $project = Project::create($data);
                $project->projectData()->createMany($projectData);
            });
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
