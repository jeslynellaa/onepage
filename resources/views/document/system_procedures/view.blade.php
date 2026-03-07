<x-layout>
    <div class="mx-auto w-full px-5 py-1">
        <h1 class="font-semibold text-gray-800">
            <a href="{{ route('document.index') }}">Document Management</a> > <a href="{{ route('document.system_procedures')}}">System Procedures</a> > {{$doc->title}}
        </h1>
        <div class="flex flex-col lg:flex-row gap-4">
            <div class="w-full lg:w-2/3">
                <div class="h-[78vh] pb-2">
                    <iframe src="{{ route('document.system_procedures.sp_preview', $doc->id) }}" class="w-full h-full border rounded-xl"></iframe>
                </div>
                <div>
                    <div>Justification</div>
                    <div class="rounded-xl p-2 border">{{$doc->justification}}</div>
                </div>
            </div>

            <div class="w-full lg:w-1/3 flex flex-col gap-4">
                <div class="border rounded-xl p-4 bg-white overflow-y-auto h-[43vh]">
                    <h2 class="font-semibold text-lg mb-3">Review Comments ({{$reviewComments->count()}})</h2>

                    @if($reviewComments->isEmpty())
                        <div class="text-sm text-gray-500 italic">
                            No review comments for this document.
                        </div>
                    @else
                        @foreach($sectionOrder as $section)
                            @if(isset($reviewComments[$section]))
                                <div class="mb-4">
                                    <h3 class="text-sm font-bold uppercase text-gray-700 mb-2">
                                        {{ $sectionLabels[$section] }}
                                    </h3>

                                    <div class="space-y-2">
                                        @foreach($reviewComments[$section] as $comment)

                                            <div class="rounded-lg border border-amber-300 bg-amber-50 px-3 py-2 text-sm">
                                                <div class="font-medium text-amber-800">
                                                    {{ $comment->user->name }}
                                                </div>

                                                <div class="text-gray-700">
                                                    {{ $comment->comment }}
                                                </div>

                                                <div class="mt-1 text-xs text-gray-500">
                                                    {{ $comment->created_at->format('M d, Y h:i A') }}
                                                </div>
                                            </div>

                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>

                <div class="border rounded-xl p-4 bg-white overflow-y-auto h-[43vh]">
                    <h2 class="font-semibold text-lg mb-3">Approval Comments ({{$approvalComments->count()}})</h2>

                    @if($approvalComments->isEmpty())
                        <div class="text-sm text-gray-500 italic">
                            No approval comments for this document.
                        </div>
                    @else
                        @foreach($sectionOrder as $section)
                            @if(isset($approvalComments[$section]))
                                <div class="mb-4">
                                    <h3 class="text-sm font-bold uppercase text-gray-700 mb-2">
                                        {{ $sectionLabels[$section] }}
                                    </h3>

                                    <div class="space-y-2">
                                        @foreach($approvalComments[$section] as $comment)

                                            <div class="rounded-lg border border-amber-300 bg-amber-50 px-3 py-2 text-sm">
                                                <div class="font-medium text-amber-800">
                                                    {{ $comment->user->name }}
                                                </div>

                                                <div class="text-gray-700">
                                                    {{ $comment->comment }}
                                                </div>

                                                <div class="mt-1 text-xs text-gray-500">
                                                    {{ $comment->created_at->format('M d, Y h:i A') }}
                                                </div>
                                            </div>

                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layout>