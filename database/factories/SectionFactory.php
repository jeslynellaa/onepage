<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Section>
 */
class SectionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'manual' => 'System Procedures',
            'section_number' => fake()->unique()->numerify('##'),
            'title' => fake()->words(3, true),
            'description' => fake()->sentence(),
        ];
    }
}
