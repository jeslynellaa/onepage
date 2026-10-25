<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * A Book Demo form submission. Not tenant-scoped: these belong to the platform
 * and are only shown to the host admin.
 */
class DemoRequest extends Model
{
    protected $guarded = [];

    protected $casts = [
        'demo_date' => 'date',
    ];

    /** When each Book Demo time slot starts (app timezone). */
    public const SLOT_STARTS = [
        'morning' => '09:00',
        'afternoon' => '13:00',
    ];

    public function demoCompany()
    {
        return $this->belongsTo(Company::class, 'demo_company_id');
    }

    /**
     * Default opening time for this prospect's demo: the start of the slot they
     * picked on the Book Demo form.
     */
    public function preferredStart(): CarbonInterface
    {
        $time = self::SLOT_STARTS[$this->demo_time] ?? self::SLOT_STARTS['morning'];

        return Carbon::parse($this->demo_date->toDateString().' '.$time);
    }
}
