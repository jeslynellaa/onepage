<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('companies')->insert([
            ['name' => 'FCU Solutions Inc.', 'slug' => 'fcu-solutions-inc', 'subscription_plan' => 'owner', 'subscription_status' => 'active', 'subscription_ends_at' => null]
        ]);
    }
}
