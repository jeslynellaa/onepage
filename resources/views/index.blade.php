<x-layout>
    <div class="min-h-screen bg-gray-50/50 pb-5">
        <div class="mx-auto px-6 pt-6">
            <div class="mb-5 flex flex-col sm:flex-row sm:items-center justify-between border-b border-gray-300">
                <div class="flex items-center gap-6">
                    <div class="flex-shrink-0">
                        @if(auth()->user()->company->logo_path)
                            <div class="h-12 sm:h-16 flex items-center justify-start">
                                <img src="{{ asset('storage/' . auth()->user()->company->logo_path) }}" 
                                    alt="{{ auth()->user()->company->name }} Logo" 
                                    class="h-full w-auto object-contain pointer-events-none drop-shadow-sm">
                            </div>
                        @else
                            <div class="h-16 w-16 bg-[#001f3f] rounded-2xl flex items-center justify-center shadow-sm">
                                <span class="text-white font-bold text-2xl uppercase">
                                    {{ substr(auth()->user()->company->name, 0, 1) }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="hidden sm:block h-10 w-px bg-gray-200"></div>

                    <div class="flex flex-col">
                        <div class="flex items-center gap-3">
                            <h2 class="text-2xl font-black text-gray-900 tracking-tight leading-tight">
                                {{ auth()->user()->company->name }}
                            </h2>
                            <span class="hidden sm:inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 uppercase tracking-tighter">
                                Active Workspace
                            </span>
                        </div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mt-1">
                            Document Management Portal
                        </p>
                    </div>
                </div>

                <div class="mt-4 sm:mt-0 flex items-center gap-4 text-right">
                    <div class="hidden lg:block">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">System Status</p>
                        <p class="text-sm font-semibold text-emerald-600 flex items-center justify-end gap-1">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span> All Systems Operational
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex justify-between items-end mb-3">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Dashboard</h1>
                    <p class="text-sm text-gray-500 mt-1">Overview of your document pipeline and team activity.</p>
                </div>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between group hover:border-blue-200 transition">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Active</p>
                        <h3 class="text-3xl font-bold text-gray-900">{{$activeCount}}</h3>
                    </div>
                    <div class="h-12 w-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition">
                        <i class="fas fa-file-signature text-xl"></i>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between group hover:border-amber-200 transition">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Drafts</p>
                        <h3 class="text-3xl font-bold text-gray-900">{{$draftCount}}</h3>
                    </div>
                    <div class="h-12 w-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition">
                        <i class="fas fa-pen-ruler text-xl"></i>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between group hover:border-purple-200 transition">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">In Review</p>
                        <h3 class="text-3xl font-bold text-gray-900">{{$reviewCount}}</h3>
                    </div>
                    <div class="h-12 w-12 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition">
                        <i class="fas fa-magnifying-glass text-xl"></i>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between group hover:border-emerald-200 transition">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Approved</p>
                        <h3 class="text-3xl font-bold text-gray-900">{{$approvalCount}}</h3>
                    </div>
                    <div class="h-12 w-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition">
                        <i class="fas fa-check-double text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center bg-white p-8 rounded-2xl border border-gray-200 mb-4 shadow">
                <div class="flex justify-center">
                    @php
                        $totalHours = $processStats->sum('average');
                        $offset = 0;
                        $colors = ['#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#06B6D4'];
                        $stages = ['Drafting', 'Review', 'Approval', 'Code Assignment'];
                    @endphp

                    <div class="relative w-56 h-56 rounded-full shadow-inner" 
                        style="background: conic-gradient(
                            @foreach($processStats as $index => $item)
                                @php 
                                    $rawPercent = ($item['average'] / max($totalHours, 1)) * 100;
                                    // Logic: Ensure even tiny slices are at least 1.5% visible
                                    $percentage = max($rawPercent, 1.5); 
                                    $nextOffset = $offset + $percentage;
                                    $color = $colors[$index % count($colors)];
                                @endphp
                                {{ $color }} {{ $offset }}% {{ $nextOffset }}%{{ !$loop->last ? ',' : '' }}
                                @php $offset = $nextOffset; @endphp
                            @endforeach
                        );">
                        <div class="absolute inset-10 bg-white rounded-full flex flex-col items-center justify-center shadow-sm">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Cycle</span>
                            <span class="text-2xl font-black text-gray-900 leading-none">{{ number_format($totalHours, 1) }}</span>
                            <span class="text-[10px] font-semibold text-gray-500 mt-1">HOURS</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <h4 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Process Breakdown</h4>
                    @foreach($processStats as $index => $item)
                        <div class="group flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 rounded-full shadow-sm" style="background-color: {{ $colors[$index % count($colors)] }}"></div>
                                <span class="text-sm font-medium text-gray-700 capitalize">{{ $stages[$index] }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-bold text-gray-900 block">{{ $item['average'] }}h</span>
                                <span class="text-[10px] text-gray-400">{{ $item['count'] }} documents</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                <div class="lg:col-span-8 bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col overflow-hidden h-[500px]">
                    <div class="flex justify-between items-center p-6 border-b border-gray-50">
                        <h2 class="font-bold text-gray-800 flex items-center gap-2">
                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                            Recent Activity
                        </h2>
                        <a href="{{route('activity.index')}}" class="text-xs font-bold text-blue-600 hover:underline">VIEW ALL</a>
                    </div>
                    
                    <div class="overflow-y-auto flex-1">
                        <table class="w-full text-sm text-left border-separate border-spacing-0">
                            <thead class="sticky top-0 bg-white/80 backdrop-blur-md z-10">
                                <tr>
                                    <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase border-b border-gray-50">Date</th>
                                    <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase border-b border-gray-50">Action</th>
                                    <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase border-b border-gray-50">Description</th>
                                    <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase border-b border-gray-50">User</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse ($logs as $log)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-500 text-xs">{{ $log->performed_at->format('M d, H:i') }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 rounded-md bg-slate-100 text-slate-600 text-[10px] font-bold">
                                                {{ strtoupper($log->action) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-700 relative group cursor-help">
                                            <span class="truncate max-w-[150px] block">
                                                {{ $log->description }}
                                            </span>

                                            <div class="absolute invisible group-hover:visible z-30 w-64 p-2 mt-1 text-xs text-white bg-gray-900 rounded-lg shadow-xl -left-2 top-full">
                                                {{ $log->description }}
                                                <div class="absolute -top-1 left-4 w-2 h-2 bg-gray-900 rotate-45"></div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <div class="h-6 w-6 rounded-full bg-gray-800 text-white flex items-center justify-center text-[10px] font-bold">
                                                    {{ substr($log->user->first_name, 0, 1) }}
                                                </div>
                                                <span class="text-gray-600 text-xs font-medium">{{$log->user->first_name}}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="py-20 text-center text-gray-400">No recent logs found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="lg:col-span-4 bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col overflow-hidden h-[500px]">
                    <div class="p-6 border-b border-gray-50">
                        <h2 class="font-bold text-gray-800">Action Requests</h2>
                    </div>
                    
                    <div class="overflow-y-auto flex-1 p-4 space-y-3 bg-gray-50/30">
                        @forelse ($allActions as $item)
                            <div class="p-4 rounded-xl bg-white border border-gray-100 shadow-sm hover:border-blue-300 hover:shadow-md transition group">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-600 text-[10px] font-bold uppercase">
                                        {{ $item['status'] }}
                                    </span>
                                    <span class="text-[10px] text-gray-400">#{{ $loop->iteration }}</span>
                                </div>
                                <p class="text-sm font-bold text-gray-800 leading-tight group-hover:text-blue-600 transition">{{ $item['title'] }}</p>
                                <div class="mt-4 flex justify-end">
                                    <a href="{{ route('document.system_procedures.view_pdf', $item['id'])}}" class="text-xs font-bold text-gray-400 group-hover:text-blue-600 flex items-center gap-1">
                                        View <i class="fas fa-arrow-right text-[10px]"></i>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="h-full flex flex-col items-center justify-center text-center p-6">
                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                    <i class="fas fa-check text-gray-300"></i>
                                </div>
                                <p class="text-sm text-gray-500 font-medium">All caught up!</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layout>