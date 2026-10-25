<x-layout>
    @php
        $durations = [24 => '1 day', 48 => '2 days', 72 => '3 days', 168 => '1 week'];
        $inputFormat = 'Y-m-d\TH:i';
    @endphp

    <div class="mx-auto w-full px-5 py-1 max-w-[1240px]">
        <!-- Header -->
        <div class="flex flex-wrap justify-between items-center gap-3 mb-4">
            <div>
                <a href="{{ route('admin.index') }}" class="text-[13px] text-[#8B93A7] hover:text-[#1F6FEB]"><i class="fa-solid fa-arrow-left mr-1"></i> Admin</a>
                <h1 class="font-semibold text-gray-800 mt-1">Demo Workspaces</h1>
                <p class="text-[13px] text-[#5B6478] mt-1">Each demo is a throwaway company with sample data. Send the prospect its link — no credentials needed. It opens at the scheduled time and is deleted after it closes.</p>
            </div>
        </div>

        <!-- Book Demo requests -->
        <div class="bg-white rounded-2xl shadow-lg p-5 mb-5 overflow-x-auto">
            <div class="font-semibold mb-1">Book Demo Requests</div>
            <p class="text-[13px] text-[#5B6478] mb-3">The opening time defaults to the start of the slot the prospect picked (Morning 9:00 AM, Afternoon 1:00 PM).</p>

            <table class="w-full text-[13px] border-separate border-spacing-0">
                <thead>
                    <tr class="text-left text-[#8B93A7] uppercase text-[11px] tracking-wide">
                        <th class="py-2 pr-3">Requested</th>
                        <th class="py-2 pr-3">Prospect</th>
                        <th class="py-2 pr-3">Team</th>
                        <th class="py-2 pr-3">Preferred slot</th>
                        <th class="py-2">Demo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requests as $req)
                        <tr class="align-top border-t border-[#E7EAF0]">
                            <td class="py-3 pr-3 text-[#5B6478] whitespace-nowrap">{{ $req->created_at->format('M j, Y') }}</td>
                            <td class="py-3 pr-3">
                                <div class="font-semibold text-[#0B1020]">{{ $req->company }}</div>
                                <div class="text-[#5B6478]">{{ $req->name }} · <a href="mailto:{{ $req->email }}" class="text-[#1F6FEB] hover:underline">{{ $req->email }}</a></div>
                            </td>
                            <td class="py-3 pr-3 whitespace-nowrap">{{ $req->team_size }}</td>
                            <td class="py-3 pr-3 whitespace-nowrap">{{ $req->demo_date->format('D, M j') }} · <span class="capitalize">{{ $req->demo_time }}</span></td>
                            <td class="py-3">
                                @if ($req->demoCompany)
                                    <span class="inline-flex items-center gap-1.5 text-[#0E9E8E] font-semibold"><i class="fa-solid fa-check"></i> Scheduled for {{ $req->demoCompany->demo_starts_at?->format('M j, g:i A') }}</span>
                                @else
                                    <form method="POST" action="{{ route('admin.demos.store') }}" class="flex flex-wrap items-center gap-2">
                                        @csrf
                                        <input type="hidden" name="demo_request_id" value="{{ $req->id }}">
                                        <input type="hidden" name="company_name" value="{{ $req->company }} (Demo)">
                                        <label class="sr-only" for="starts-{{ $req->id }}">Opens</label>
                                        <input id="starts-{{ $req->id }}" type="datetime-local" name="starts_at" required
                                            value="{{ $req->preferredStart()->format($inputFormat) }}"
                                            class="rounded-lg border border-[#D7DBE4] px-2 py-1.5 text-[13px]">
                                        <label class="sr-only" for="hours-{{ $req->id }}">Open for</label>
                                        <select id="hours-{{ $req->id }}" name="hours" class="rounded-lg border border-[#D7DBE4] px-2 py-1.5 text-[13px]">
                                            @foreach ($durations as $value => $label)
                                                <option value="{{ $value }}" @selected($value === 72)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="bg-[#1F6FEB] hover:bg-[#1A5FD0] text-white font-semibold px-3 py-1.5 rounded-lg cursor-pointer duration-200">Create demo</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-4 text-[#8B93A7]">No Book Demo requests yet. New submissions from the Book Demo page appear here.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Manual demo -->
        <div class="bg-white rounded-2xl shadow-lg p-5 mb-5">
            <div class="font-semibold mb-1">Schedule a Demo Manually</div>
            <p class="text-[13px] text-[#5B6478] mb-3">For prospects who didn't use the Book Demo form.</p>

            <form method="POST" action="{{ route('admin.demos.store') }}" class="flex flex-wrap items-end gap-3 text-[13px]">
                @csrf
                <div>
                    <label for="company_name" class="block text-[11px] font-bold uppercase text-[#8B93A7] tracking-wide mb-1">Company name</label>
                    <input id="company_name" name="company_name" required value="{{ old('company_name') }}" placeholder="Acme Corp (Demo)"
                        class="rounded-lg border border-[#D7DBE4] px-3 py-2 w-64">
                </div>
                <div>
                    <label for="starts_at" class="block text-[11px] font-bold uppercase text-[#8B93A7] tracking-wide mb-1">Opens</label>
                    <input id="starts_at" type="datetime-local" name="starts_at" required
                        value="{{ old('starts_at', now()->format($inputFormat)) }}"
                        class="rounded-lg border border-[#D7DBE4] px-3 py-2">
                </div>
                <div>
                    <label for="hours" class="block text-[11px] font-bold uppercase text-[#8B93A7] tracking-wide mb-1">Open for</label>
                    <select id="hours" name="hours" class="rounded-lg border border-[#D7DBE4] px-3 py-2">
                        @foreach ($durations as $value => $label)
                            <option value="{{ $value }}" @selected((int) old('hours', 72) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-[#1F6FEB] hover:bg-[#1A5FD0] text-white font-semibold px-4 py-2 rounded-lg cursor-pointer duration-200">Create demo</button>
            </form>
        </div>

        <!-- Existing demos -->
        <div class="bg-white rounded-2xl shadow-lg p-5 mb-8 overflow-x-auto">
            <div class="font-semibold mb-3">Demos</div>

            <table class="w-full text-[13px] border-separate border-spacing-0">
                <thead>
                    <tr class="text-left text-[#8B93A7] uppercase text-[11px] tracking-wide">
                        <th class="py-2 pr-3">Company</th>
                        <th class="py-2 pr-3">Status</th>
                        <th class="py-2 pr-3">Opens</th>
                        <th class="py-2 pr-3">Closes</th>
                        <th class="py-2 pr-3">Link</th>
                        <th class="py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($demos as $demo)
                        @php
                            [$status, $badge] = match (true) {
                                $demo->demoExpired() => ['Closed', 'bg-gray-100 text-gray-600'],
                                $demo->demoNotYetOpen() => ['Scheduled', 'bg-[#EEF4FF] text-[#1F6FEB]'],
                                default => ['Open', 'bg-[#E6FAF6] text-[#0E9E8E]'],
                            };
                        @endphp
                        <tr class="align-middle border-t border-[#E7EAF0] {{ session('new_demo_id') === $demo->id ? 'bg-[#FFFBEA]' : '' }}">
                            <td class="py-3 pr-3 font-semibold text-[#0B1020]">{{ $demo->name }}</td>
                            <td class="py-3 pr-3"><span class="px-2 py-0.5 rounded-full text-[12px] font-semibold {{ $badge }}">{{ $status }}</span></td>
                            <td class="py-3 pr-3 whitespace-nowrap">{{ $demo->demo_starts_at?->format('M j, g:i A') ?? '—' }}</td>
                            <td class="py-3 pr-3 whitespace-nowrap">{{ $demo->demo_expires_at?->format('M j, g:i A') ?? '—' }}</td>
                            <td class="py-3 pr-3">
                                @unless ($demo->demoExpired())
                                    <button type="button"
                                        x-data="{ copied: false }"
                                        @click="navigator.clipboard.writeText(@js(\App\Services\DemoProvisioner::linkFor($demo))); copied = true; setTimeout(() => copied = false, 2000)"
                                        class="inline-flex items-center gap-1.5 border border-[#D7DBE4] hover:border-[#1F6FEB] hover:text-[#1F6FEB] px-2.5 py-1 rounded-lg cursor-pointer duration-200">
                                        <i class="fa-regular fa-copy"></i>
                                        <span x-text="copied ? 'Copied!' : 'Copy link'">Copy link</span>
                                    </button>
                                @else
                                    <span class="text-[#8B93A7]">—</span>
                                @endunless
                            </td>
                            <td class="py-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <form method="POST" action="{{ route('admin.demos.extend', $demo) }}" class="inline-flex items-center gap-1">
                                        @csrf
                                        @method('PUT')
                                        <label class="sr-only" for="extend-{{ $demo->id }}">Extend by</label>
                                        <select id="extend-{{ $demo->id }}" name="hours" class="rounded-lg border border-[#D7DBE4] px-2 py-1 text-[13px]">
                                            @foreach ($durations as $value => $label)
                                                <option value="{{ $value }}">+{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="text-[#1F6FEB] font-semibold hover:underline cursor-pointer">{{ $demo->demoExpired() ? 'Reopen' : 'Extend' }}</button>
                                    </form>
                                    @unless ($demo->demoExpired())
                                        <form method="POST" action="{{ route('admin.demos.end', $demo) }}"
                                            onsubmit="return confirm('Close this demo now? The prospect loses access immediately and its data is deleted within the hour.')">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="text-red-600 font-semibold hover:underline cursor-pointer">End now</button>
                                        </form>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-4 text-[#8B93A7]">No demos yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layout>
