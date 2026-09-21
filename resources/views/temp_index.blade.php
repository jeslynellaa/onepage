<x-layout>
    <div class="px-6 pt-6 pb-10">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="font-extrabold text-[26px] tracking-[-0.03em] text-[#0B1020]">Dashboard</h1>
                <p class="text-[13.5px] text-[#5B6478] mt-1">Overview of your document pipeline and team activity.</p>
            </div>

            <div class="flex items-center gap-3 min-w-0 bg-white p-4 rounded-2xl shadow-[0_8px_26px_rgba(11,16,32,.06)]">
                @if(auth()->user()->company->logo_path)
                    <div class="h-14 flex items-center flex-none">
                        <img src="{{ asset('storage/' . auth()->user()->company->logo_path) }}"
                            alt="{{ auth()->user()->company->name }} Logo"
                            class="h-full w-auto max-w-[240px] object-contain">
                    </div>
                @else
                    <div class="h-12 w-12 rounded-xl bg-[#0B1020] flex items-center justify-center flex-none">
                        <span class="text-white font-bold text-base uppercase">{{ substr(auth()->user()->company->name, 0, 1) }}</span>
                    </div>
                @endif
                <span class="text-[15px] font-bold text-[#0B1020] truncate max-w-[220px]">{{ auth()->user()->company->name }}</span>
            </div>
        </div>

        {{-- WORKFLOW STATUS --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl shadow-[0_8px_26px_rgba(11,16,32,.06)] border border-[#E7EAF0] p-6 flex items-center justify-between group hover:border-[#10C9B6]/50 transition">
                <div>
                    <p class="text-xs font-bold text-[#8B93A7] uppercase tracking-wider mb-1">Active</p>
                    <h3 class="text-3xl font-extrabold text-[#0B1020] tracking-[-0.02em]">{{ $activeCount }}</h3>
                </div>
                <div class="h-12 w-12 bg-[#E6FAF6] text-[#0E9E8E] rounded-2xl flex items-center justify-center group-hover:scale-110 transition">
                    <i class="fa-solid fa-layer-group text-xl"></i>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-[0_8px_26px_rgba(11,16,32,.06)] border border-[#E7EAF0] p-6 flex items-center justify-between group hover:border-[#8B93A7]/50 transition">
                <div>
                    <p class="text-xs font-bold text-[#8B93A7] uppercase tracking-wider mb-1">Drafts</p>
                    <h3 class="text-3xl font-extrabold text-[#0B1020] tracking-[-0.02em]">{{ $draftCount }}</h3>
                </div>
                <div class="h-12 w-12 bg-[#F0F2F6] text-[#5B6478] rounded-2xl flex items-center justify-center group-hover:scale-110 transition">
                    <i class="fas fa-pen-ruler text-xl"></i>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-[0_8px_26px_rgba(11,16,32,.06)] border border-[#E7EAF0] p-6 flex items-center justify-between group hover:border-[#1F6FEB]/40 transition">
                <div>
                    <p class="text-xs font-bold text-[#8B93A7] uppercase tracking-wider mb-1">In Review</p>
                    <h3 class="text-3xl font-extrabold text-[#0B1020] tracking-[-0.02em]">{{ $reviewCount }}</h3>
                </div>
                <div class="h-12 w-12 bg-[#E8F1FF] text-[#1F6FEB] rounded-2xl flex items-center justify-center group-hover:scale-110 transition">
                    <i class="fas fa-magnifying-glass text-xl"></i>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-[0_8px_26px_rgba(11,16,32,.06)] border border-[#E7EAF0] p-6 flex items-center justify-between group hover:border-[#7C5CFC]/40 transition">
                <div>
                    <p class="text-xs font-bold text-[#8B93A7] uppercase tracking-wider mb-1">For Approval</p>
                    <h3 class="text-3xl font-extrabold text-[#0B1020] tracking-[-0.02em]">{{ $approvalCount }}</h3>
                </div>
                <div class="h-12 w-12 bg-[#F1EDFF] text-[#7C5CFC] rounded-2xl flex items-center justify-center group-hover:scale-110 transition">
                    <i class="fas fa-check-double text-xl"></i>
                </div>
            </div>
        </div>

        {{-- PROCESS BREAKDOWN — the "Open Actions" and "Document Working Time" ring
             cards from the landing page (resources/views/landing/index.blade.php:151),
             plus the "Cycle time" gradient card (line 167). Equal-width columns,
             collapsing to one card per row on small screens. --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            @if($inFlightTotal > 0)
                <div class="bg-white rounded-[18px] px-6 py-[22px] shadow-[0_8px_26px_rgba(11,16,32,.07)] flex flex-col">
                    <div class="text-[13px] font-semibold mb-4 text-[#0B1020]">Open Actions</div>
                    <div class="flex items-center gap-[22px]">
                        <svg viewBox="0 0 100 100" class="w-[100px] h-[100px] flex-none -rotate-90">
                            <circle cx="50" cy="50" r="38" fill="none" stroke="#EEF1F6" stroke-width="16" />
                            <circle cx="50" cy="50" r="38" fill="none" stroke="#1F6FEB" stroke-width="16" stroke-dasharray="{{ $draftArcLength }} {{ $circumference }}" />
                            <circle cx="50" cy="50" r="38" fill="none" stroke="#10C9B6" stroke-width="16" stroke-dasharray="{{ $reviewArcLength }} {{ $circumference }}" stroke-dashoffset="{{ $reviewArcOffset }}" />
                            <circle cx="50" cy="50" r="38" fill="none" stroke="#B8C4D8" stroke-width="16" stroke-dasharray="{{ $approvalArcLength }} {{ $circumference }}" stroke-dashoffset="{{ $approvalArcOffset }}" />
                        </svg>
                        <div class="flex flex-col gap-[11px]">
                            <div class="flex items-center gap-[9px] text-[12.5px]"><span class="w-2.5 h-2.5 rounded-sm bg-[#1F6FEB] block"></span><span class="font-semibold text-[#0B1020]">Drafting</span><span class="text-[#8B93A7]">{{ $draftSharePct }}%</span></div>
                            <div class="flex items-center gap-[9px] text-[12.5px]"><span class="w-2.5 h-2.5 rounded-sm bg-[#10C9B6] block"></span><span class="font-semibold text-[#0B1020]">Reviews</span><span class="text-[#8B93A7]">{{ $reviewSharePct }}%</span></div>
                            <div class="flex items-center gap-[9px] text-[12.5px]"><span class="w-2.5 h-2.5 rounded-sm bg-[#B8C4D8] block"></span><span class="font-semibold text-[#0B1020]">Approval</span><span class="text-[#8B93A7]">{{ $approvalSharePct }}%</span></div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-[18px] px-6 py-6 shadow-[0_8px_26px_rgba(11,16,32,.07)] flex items-center">
                    <p class="text-sm font-semibold text-[#5B6478]">No documents currently in motion</p>
                </div>
            @endif

            @if($totalLifecycleHours > 0)
                <div class="bg-white rounded-[18px] px-6 py-[22px] shadow-[0_8px_26px_rgba(11,16,32,.07)] flex flex-col">
                    <div class="text-[13px] font-semibold mb-4 text-[#0B1020]">Document Working Time</div>
                    <div class="flex items-center gap-[22px]">
                        <svg viewBox="0 0 100 100" class="w-[100px] h-[100px] flex-none -rotate-90">
                            <circle cx="50" cy="50" r="38" fill="none" stroke="#EEF1F6" stroke-width="16" />
                            <circle cx="50" cy="50" r="38" fill="none" stroke="#1F6FEB" stroke-width="16" stroke-dasharray="{{ $draftLifecycleArcLength }} {{ $circumference }}" />
                            <circle cx="50" cy="50" r="38" fill="none" stroke="#10C9B6" stroke-width="16" stroke-dasharray="{{ $reviewLifecycleArcLength }} {{ $circumference }}" stroke-dashoffset="{{ $reviewLifecycleArcOffset }}" />
                            <circle cx="50" cy="50" r="38" fill="none" stroke="#B8C4D8" stroke-width="16" stroke-dasharray="{{ $approvalLifecycleArcLength }} {{ $circumference }}" stroke-dashoffset="{{ $approvalLifecycleArcOffset }}" />
                            <circle cx="50" cy="50" r="38" fill="none" stroke="#5B6478" stroke-width="16" stroke-dasharray="{{ $pendingCodeLifecycleArcLength }} {{ $circumference }}" stroke-dashoffset="{{ $pendingCodeLifecycleArcOffset }}" />
                        </svg>
                        <div class="flex flex-col gap-[11px]">
                            <div class="flex items-center gap-[9px] text-[12.5px]"><span class="w-2.5 h-2.5 rounded-sm bg-[#1F6FEB] block"></span><span class="font-semibold text-[#0B1020]">Draft</span><span class="text-[#8B93A7]">{{ $draftLifecyclePct }}%</span></div>
                            <div class="flex items-center gap-[9px] text-[12.5px]"><span class="w-2.5 h-2.5 rounded-sm bg-[#10C9B6] block"></span><span class="font-semibold text-[#0B1020]">Review</span><span class="text-[#8B93A7]">{{ $reviewLifecyclePct }}%</span></div>
                            <div class="flex items-center gap-[9px] text-[12.5px]"><span class="w-2.5 h-2.5 rounded-sm bg-[#B8C4D8] block"></span><span class="font-semibold text-[#0B1020]">Approval</span><span class="text-[#8B93A7]">{{ $approvalLifecyclePct }}%</span></div>
                            <div class="flex items-center gap-[9px] text-[12.5px]"><span class="w-2.5 h-2.5 rounded-sm bg-[#5B6478] block"></span><span class="font-semibold text-[#0B1020]">Pending Code</span><span class="text-[#8B93A7]">{{ $pendingCodeLifecyclePct }}%</span></div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-[18px] px-6 py-6 shadow-[0_8px_26px_rgba(11,16,32,.07)] flex items-center">
                    <p class="text-sm font-semibold text-[#5B6478]">Not enough activity yet to show stage timing</p>
                </div>
            @endif

            <div class="bg-gradient-to-br from-[#10C9B6] to-[#1F6FEB] rounded-[18px] px-6 py-[22px] text-white shadow-[0_16px_34px_rgba(31,111,235,.28)] flex flex-col justify-center">
                <div class="flex items-center gap-2.5 mb-5">
                    <span class="w-9 h-9 rounded-full bg-white/25 flex items-center justify-center">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    </span>
                    <span class="text-[14.5px] font-bold">Cycle time</span>
                </div>
                <div class="flex items-baseline gap-[7px] mb-2">
                    @if($totalLifecycleHours > 0)
                        @if($totalLifecycleHours >= 48)
                            <span class="text-[40px] font-extrabold tracking-[-0.03em] leading-none">{{ number_format($totalLifecycleDays, 1) }}</span>
                            <span class="text-sm opacity-85">days avg</span>
                        @else
                            <span class="text-[40px] font-extrabold tracking-[-0.03em] leading-none">{{ number_format($totalLifecycleHours, 1) }}</span>
                            <span class="text-sm opacity-85">hrs avg</span>
                        @endif
                    @else
                        <span class="text-[40px] font-extrabold tracking-[-0.03em] leading-none">—</span>
                        <span class="text-sm opacity-85">no data yet</span>
                    @endif
                </div>
                <p class="text-[12.5px] leading-[1.5] text-white/75 m-0">Average time from first draft to an assigned code, across your organization.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
            <div class="lg:col-span-8 bg-white rounded-2xl shadow-[0_8px_26px_rgba(11,16,32,.06)] border border-[#E7EAF0] flex flex-col overflow-hidden h-[500px]" x-data="{ filter: 'all' }">
                <div class="flex justify-between items-center p-6 border-b border-[#F0F2F6]">
                    <h2 class="font-bold text-[#0B1020] flex items-center gap-2 text-[15px]">
                        <span class="w-2 h-2 bg-[#1F6FEB] rounded-full"></span>
                        Recent Activity
                    </h2>
                    <a href="{{route('activity.index')}}" class="text-xs font-bold text-[#1F6FEB] hover:text-[#0B1020] duration-200">VIEW ALL</a>
                </div>

                @if($logs->isNotEmpty())
                    <div class="flex items-center gap-2 px-6 py-2 border-b border-[#F0F2F6] overflow-x-auto">
                        <button type="button" @click="filter = 'all'" :class="filter === 'all' ? 'bg-[#1F6FEB] text-white' : 'bg-[#F0F2F6] text-[#5B6478] hover:bg-[#E7EAF0]'" class="px-2.5 py-1 rounded-full text-[9pt] font-bold uppercase whitespace-nowrap transition">All</button>
                        @foreach ($logs->pluck('action')->unique() as $actionName)
                            <button type="button" @click="filter = '{{ $actionName }}'" :class="filter === '{{ $actionName }}' ? 'bg-[#1F6FEB] text-white' : 'bg-[#F0F2F6] text-[#5B6478] hover:bg-[#E7EAF0]'" class="px-2.5 py-1 rounded-full text-[9pt] font-bold uppercase whitespace-nowrap transition">{{ strtoupper($actionName) }}</button>
                        @endforeach
                    </div>
                @endif

                <div class="overflow-y-auto flex-1">
                    <table class="w-full text-sm text-left border-separate border-spacing-0">
                        <thead class="sticky top-0 bg-white/90 backdrop-blur-md z-10">
                            <tr>
                                <th class="px-6 py-4 text-xs font-bold text-[#8B93A7] uppercase border-b border-[#F0F2F6]">Date</th>
                                <th class="px-6 py-4 text-xs font-bold text-[#8B93A7] uppercase border-b border-[#F0F2F6]">Action</th>
                                <th class="px-6 py-4 text-xs font-bold text-[#8B93A7] uppercase border-b border-[#F0F2F6]">Description</th>
                                <th class="px-6 py-4 text-xs font-bold text-[#8B93A7] uppercase border-b border-[#F0F2F6]">User</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F0F2F6]">
                            @forelse ($logs as $log)
                                <tr class="hover:bg-[#F5F7FA] transition" x-show="filter === 'all' || filter === '{{ $log->action }}'">
                                    <td class="px-6 py-4 whitespace-nowrap text-[#8B93A7] text-xs">{{ $log->performed_at->format('M d, H:i') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded-md bg-[#F0F2F6] text-[#5B6478] text-xs font-bold">
                                            {{ strtoupper($log->action) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-[#0B1020] relative group cursor-help">
                                        <span class="truncate max-w-[150px] block">
                                            {{ $log->description }}
                                        </span>

                                        <div class="absolute invisible group-hover:visible z-30 w-64 p-2 mt-1 text-xs text-white bg-[#0B1020] rounded-lg shadow-xl -left-2 top-full">
                                            {{ $log->description }}
                                            <div class="absolute -top-1 left-4 w-2 h-2 bg-[#0B1020] rotate-45"></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="h-6 w-6 rounded-full bg-[#0B1020] text-white flex items-center justify-center text-xs font-bold">
                                                {{ substr($log->user->first_name, 0, 1) }}
                                            </div>
                                            <span class="text-[#5B6478] text-xs font-medium">{{$log->user->first_name}}</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-20 text-center text-[#8B93A7]">No recent logs found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="lg:col-span-4 bg-white rounded-2xl shadow-[0_8px_26px_rgba(11,16,32,.06)] border border-[#E7EAF0] flex flex-col overflow-hidden h-[500px]">
                <div class="p-6 border-b border-[#F0F2F6] flex items-center justify-between">
                    <h2 class="font-bold text-[#0B1020] text-[15px]">Action Requests</h2>
                    @if($allActions->isNotEmpty())
                        <span class="text-xs font-bold text-[#8B93A7]">{{ $allActions->count() }}</span>
                    @endif
                </div>

                <div class="overflow-y-auto flex-1 p-4 space-y-5 bg-[#F5F7FA]/50">
                    @if($allActions->isEmpty())
                        <div class="h-full flex flex-col items-center justify-center text-center p-6">
                            <div class="w-12 h-12 bg-[#F0F2F6] rounded-full flex items-center justify-center mb-3">
                                <i class="fas fa-check text-[#B8C4D8]"></i>
                            </div>
                            <p class="text-sm text-[#5B6478] font-medium">All caught up!</p>
                        </div>
                    @else
                        @php
                            $groupedActions = $allActions->groupBy('status');
                            $actionGroupMeta = [
                                'For Review'   => ['label' => 'Needs Your Review', 'icon' => 'fa-magnifying-glass'],
                                'For Approval' => ['label' => 'Needs Your Approval', 'icon' => 'fa-check-double'],
                                'Pending Code' => ['label' => 'Needs Code Assignment', 'icon' => 'fa-hashtag'],
                            ];
                            $actionGroupCap = 3;
                        @endphp
                        @foreach ($actionGroupMeta as $status => $meta)
                            @continue(!isset($groupedActions[$status]))
                            @php $items = $groupedActions[$status]; @endphp
                            <div @if($items->count() > $actionGroupCap) x-data="{ expanded: false }" @endif>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="flex items-center gap-1.5 text-xs font-bold text-[#8B93A7] uppercase tracking-wider">
                                        <i class="fas {{ $meta['icon'] }} text-xs"></i>
                                        {{ $meta['label'] }}
                                    </span>
                                    <span class="text-xs font-bold text-[#8B93A7]">{{ $items->count() }}</span>
                                </div>

                                <div class="space-y-2">
                                    @foreach ($items as $idx => $item)
                                        <div @if($idx >= $actionGroupCap) x-show="expanded" @endif class="p-4 rounded-xl bg-white border border-[#E7EAF0] shadow-sm hover:border-[#1F6FEB]/50 hover:shadow-md transition group">
                                            <p class="text-sm font-bold text-[#0B1020] leading-tight group-hover:text-[#1F6FEB] transition">{{ $item['title'] }}</p>
                                            <div class="mt-4 flex justify-end">
                                                <a href="{{ route('document.system_procedures.view_pdf', $item['id'])}}" class="text-xs font-bold text-[#8B93A7] group-hover:text-[#1F6FEB] flex items-center gap-1">
                                                    View <i class="fas fa-arrow-right text-xs"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @if($items->count() > $actionGroupCap)
                                    <button type="button" @click="expanded = !expanded" class="mt-2 text-xs font-bold text-[#1F6FEB] hover:underline" x-text="expanded ? 'Show less' : '+{{ $items->count() - $actionGroupCap }} more'"></button>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-temp-layout>
