<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sections')->insert([
            ['manual' => 'System Procedures', 'section_number' => '01', 'title' => 'Business Planning', 'description' => ''],
            ['manual' => 'System Procedures', 'section_number' => '02', 'title' => 'Business Development', 'description' => ''],
            ['manual' => 'System Procedures', 'section_number' => '03', 'title' => 'Project Planning and Implementation', 'description' => ''],
            ['manual' => 'System Procedures', 'section_number' => '04', 'title' => 'Project Evaluation', 'description' => ''],
            ['manual' => 'System Procedures', 'section_number' => '05', 'title' => 'Project Completion', 'description' => ''],
            ['manual' => 'System Procedures', 'section_number' => '06', 'title' => 'Asset Management', 'description' => ''],
            ['manual' => 'System Procedures', 'section_number' => '07', 'title' => 'Maintenance', 'description' => ''],
            ['manual' => 'System Procedures', 'section_number' => '08', 'title' => 'Human Resource Management', 'description' => ''],
            ['manual' => 'System Procedures', 'section_number' => '09', 'title' => 'Financial Resource Management', 'description' => ''],
            ['manual' => 'System Procedures', 'section_number' => '10', 'title' => 'Documented Information Management', 'description' => ''],
            ['manual' => 'System Procedures', 'section_number' => '11', 'title' => 'Continual Improvement', 'description' => '']
        ]);
    }
}
