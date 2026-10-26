<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SectionRenumberTest extends TestCase
{
    use RefreshDatabase;

    private Company $client;

    protected function setUp(): void
    {
        parent::setUp();

        $host = Company::factory()->create();
        $this->actingAs(User::factory()->create(['company_id' => $host->id, 'role' => 'Admin']));

        $this->client = Company::factory()->create();
        foreach (['One', 'Two', 'Three', 'Four', 'Five'] as $i => $title) {
            Section::factory()->create([
                'company_id' => $this->client->id,
                'section_number' => str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                'title' => $title,
                'description' => strtoupper($title),
            ]);
        }
    }

    private function numbering(Company $company): array
    {
        return Section::where('company_id', $company->id)->orderBy('section_number')->pluck('title', 'section_number')->all();
    }

    private function update(string $title, int $number)
    {
        $section = Section::where('company_id', $this->client->id)->where('title', $title)->firstOrFail();

        return $this->put(route('admin.client.sections.update', [$this->client->id, $section->id]), [
            'section_number' => $number,
            'title' => $section->title,
            'description' => $section->description,
        ]);
    }

    public function test_moving_a_section_up_shifts_the_ones_in_between_down(): void
    {
        $this->update('Five', 2)->assertSessionHasNoErrors();

        $this->assertSame(
            ['01' => 'One', '02' => 'Five', '03' => 'Two', '04' => 'Three', '05' => 'Four'],
            $this->numbering($this->client)
        );
    }

    public function test_moving_a_section_down_shifts_the_ones_in_between_up(): void
    {
        $this->update('Two', 4)->assertSessionHasNoErrors();

        $this->assertSame(
            ['01' => 'One', '02' => 'Three', '03' => 'Four', '04' => 'Two', '05' => 'Five'],
            $this->numbering($this->client)
        );
    }

    public function test_number_outside_the_section_count_is_rejected(): void
    {
        $this->update('Two', 6)->assertSessionHasErrors('section_number');
        $this->update('Two', 0)->assertSessionHasErrors('section_number');

        $this->assertSame(
            ['01' => 'One', '02' => 'Two', '03' => 'Three', '04' => 'Four', '05' => 'Five'],
            $this->numbering($this->client)
        );
    }

    public function test_other_companies_sections_are_untouched(): void
    {
        $other = Company::factory()->create();
        Section::factory()->create(['company_id' => $other->id, 'section_number' => '02', 'title' => 'Other']);

        $this->update('Five', 1)->assertSessionHasNoErrors();

        $this->assertSame(['02' => 'Other'], $this->numbering($other));
    }
}
