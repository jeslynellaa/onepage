{{--
    Action toolbar for a document's view page. Pass a URL only for actions the current user may
    take (check the document-type policy at the call site); a null URL hides that action.
    Desktop: inline toolbar. Mobile: the main next step in a bar pinned to the bottom of the
    screen, with the rest in a "More" sheet.
--}}
@props([
    'status' => null,             // shows a status pill when given
    'label' => null,              // code/title used in dialog headings
    'openUrl' => null,            // opens the document file in a new tab
    'wordUrl' => null,            // downloads an editable Word copy
    'editUrl' => null,
    'editLabel' => 'Edit',
    'historyUrl' => null,
    'sendUrl' => null,
    'reviewUrl' => null,
    'approveUrl' => null,
    'commentUrl' => null,
    'assignCodeUrl' => null,
    'suggestedCode' => null,
    'suggestedRevision' => null,
    'archiveUrl' => null,
    'rejectNeedsReason' => false, // reviewer/approver must give a reason to fail/not approve
    'rejectIsFinal' => false,     // a failed review / non-approval can't be revised afterwards
])

@php
    $rejectUrl = $reviewUrl ?? $approveUrl;
    $rejectLabel = $reviewUrl ? 'Fail Review' : 'Not Approve';

    // Ordered by importance: the first workflow action becomes the mobile bar's main button.
    $actions = collect([
        $sendUrl ? ['label' => 'Send for Review', 'icon' => 'fa-paper-plane', 'tone' => 'primary', 'dialog' => 'send'] : null,
        $reviewUrl ? ['label' => 'Pass Review', 'icon' => 'fa-check', 'tone' => 'primary', 'dialog' => 'pass'] : null,
        $approveUrl ? ['label' => 'Approve', 'icon' => 'fa-check-double', 'tone' => 'primary', 'dialog' => 'approve'] : null,
        $assignCodeUrl ? ['label' => 'Assign Code', 'icon' => 'fa-file-pen', 'tone' => 'primary', 'dialog' => 'assign'] : null,
        $commentUrl ? ['label' => 'Comment & Send Back', 'icon' => 'fa-comment', 'tone' => 'workflow', 'href' => $commentUrl] : null,
        $rejectUrl ? ['label' => $rejectLabel, 'icon' => 'fa-xmark', 'tone' => 'danger', 'dialog' => 'reject'] : null,
        $editUrl ? ['label' => $editLabel, 'icon' => 'fa-pen', 'tone' => 'neutral', 'href' => $editUrl] : null,
        $openUrl ? ['label' => 'Open in New Tab', 'icon' => 'fa-arrow-up-right-from-square', 'tone' => 'neutral', 'href' => $openUrl, 'newTab' => true] : null,
        $wordUrl ? ['label' => 'Download Word', 'icon' => 'fa-file-word', 'tone' => 'neutral', 'href' => $wordUrl] : null,
        $historyUrl ? ['label' => 'Revision History', 'icon' => 'fa-clock-rotate-left', 'tone' => 'neutral', 'href' => $historyUrl] : null,
        $archiveUrl ? ['label' => 'Archive', 'icon' => 'fa-box-archive', 'tone' => 'archive', 'dialog' => 'archive'] : null,
    ])->filter()->values();

    // Desktop: secondary actions on the left, workflow decisions on the right with the main one last.
    $toolbarGroups = collect([
        $actions->whereIn('tone', ['neutral', 'archive']),
        $actions->whereIn('tone', ['danger', 'workflow'])->reverse()->concat($actions->where('tone', 'primary')),
    ])->reject->isEmpty();

    $mobilePrimary = $actions->first();
    $mobileMore = $actions->slice(1);

    $hint = match (true) {
        (bool) $reviewUrl => 'This document is waiting for your review.',
        (bool) $approveUrl => 'This document is waiting for your approval.',
        (bool) $assignCodeUrl => 'Approved — assign the final code to make it Active.',
        (bool) $sendUrl => $status === 'For Revision'
            ? 'Sent back with comments — revise it, then send it for review again.'
            : 'Send it for review once it\'s ready.',
        default => null,
    };

    $statusTone = match (true) {
        $status === 'Active' => 'bg-[#3de3b1]/10 text-[#2db68e]',
        in_array($status, ['Review not Passed', 'Not Approved'], true) => 'bg-red-50 text-red-600',
        str_contains((string) $status, 'Review') || str_contains((string) $status, 'Approval') || $status === 'Pending Code' => 'bg-[#575df9]/10 text-[#575df9]',
        default => 'bg-gray-100 text-gray-500',
    };

    $button = 'inline-flex items-center justify-center gap-2 rounded-xl text-sm font-bold whitespace-nowrap transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#575df9]/40';
    $tones = [
        'primary' => 'bg-[#575df9] text-white shadow-sm hover:bg-[#464bd4]',
        'workflow' => 'bg-white text-gray-700 border border-gray-200 hover:border-[#575df9] hover:text-[#575df9]',
        'neutral' => 'bg-white text-gray-700 border border-gray-200 hover:border-[#575df9] hover:text-[#575df9]',
        'danger' => 'bg-white text-red-600 border border-red-200 hover:bg-red-50',
        'archive' => 'text-gray-500 hover:text-red-600 hover:bg-red-50',
    ];
@endphp

@if ($actions->isNotEmpty() || $status || $hint)
<div x-data="{ dialog: null, sheet: false }" {{ $attributes->merge(['class' => 'doc-actions']) }}>

    {{-- Status + toolbar --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 bg-white border border-gray-100 rounded-2xl shadow-sm px-4 py-3">
        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 min-w-0">
            @if ($status)
                <span class="px-2.5 py-1 rounded-full font-bold text-[11px] uppercase tracking-wide {{ $statusTone }}">{{ $status }}</span>
            @endif
            @if ($hint)
                <span class="text-sm text-gray-600">{{ $hint }}</span>
            @endif
        </div>

        @if ($actions->isNotEmpty())
            <div class="hidden md:flex flex-wrap items-center justify-end gap-2">
                @foreach ($toolbarGroups as $group)
                    @unless ($loop->first)
                        <span class="mx-1 h-6 w-px bg-gray-200" aria-hidden="true"></span>
                    @endunless
                    @foreach ($group as $action)
                        @if (isset($action['href']))
                            <a href="{{ $action['href'] }}" @if ($action['newTab'] ?? false) target="_blank" rel="noopener" @endif
                                class="{{ $button }} h-10 px-4 {{ $tones[$action['tone']] }}">
                                <i class="fa-solid {{ $action['icon'] }} text-xs"></i>{{ $action['label'] }}
                            </a>
                        @else
                            <button type="button" @click="dialog = '{{ $action['dialog'] }}'"
                                class="{{ $button }} h-10 px-4 {{ $tones[$action['tone']] }}">
                                <i class="fa-solid {{ $action['icon'] }} text-xs"></i>{{ $action['label'] }}
                            </button>
                        @endif
                    @endforeach
                @endforeach
            </div>
        @endif
    </div>

    @if ($actions->isNotEmpty())
        {{-- Mobile: pinned bar (the layout pads the body while .doc-actions-bar is on the page) --}}
        <div class="doc-actions-bar md:hidden fixed bottom-0 right-0 z-10 flex gap-2 border-t border-gray-200 bg-white/95 backdrop-blur px-3 pt-3 pb-[max(0.75rem,env(safe-area-inset-bottom))] transition-all duration-300"
            :class="collapsed ? 'left-16' : 'left-60'">
            @if ($mobileMore->isNotEmpty())
                <button type="button" @click="sheet = true" aria-label="More actions"
                    class="{{ $button }} h-12 w-12 shrink-0 bg-white text-gray-700 border border-gray-200">
                    <i class="fa-solid fa-ellipsis"></i>
                </button>
            @endif
            @if (isset($mobilePrimary['href']))
                <a href="{{ $mobilePrimary['href'] }}" @if ($mobilePrimary['newTab'] ?? false) target="_blank" rel="noopener" @endif
                    class="{{ $button }} h-12 flex-1 px-4 {{ $tones['primary'] }}">
                    <i class="fa-solid {{ $mobilePrimary['icon'] }} text-xs"></i>{{ $mobilePrimary['label'] }}
                </a>
            @else
                <button type="button" @click="dialog = '{{ $mobilePrimary['dialog'] }}'"
                    class="{{ $button }} h-12 flex-1 px-4 {{ $tones['primary'] }}">
                    <i class="fa-solid {{ $mobilePrimary['icon'] }} text-xs"></i>{{ $mobilePrimary['label'] }}
                </button>
            @endif
        </div>

        {{-- Mobile: "More" sheet --}}
        @if ($mobileMore->isNotEmpty())
            <div x-show="sheet" x-cloak x-transition.opacity @click.self="sheet = false" @keydown.escape.window="sheet = false"
                class="md:hidden fixed inset-0 z-40 flex items-end bg-gray-900/40" role="dialog" aria-modal="true" aria-label="More actions">
                <div class="w-full rounded-t-3xl bg-white px-2 pt-2 pb-[max(0.5rem,env(safe-area-inset-bottom))] shadow-xl">
                    <div class="mx-auto my-2 h-1 w-10 rounded-full bg-gray-200" aria-hidden="true"></div>
                    <ul class="py-1">
                        @foreach ($mobileMore as $action)
                            @php($rowTone = in_array($action['tone'], ['danger', 'archive'], true) ? 'text-red-600' : 'text-gray-800')
                            <li>
                                @if (isset($action['href']))
                                    <a href="{{ $action['href'] }}" @if ($action['newTab'] ?? false) target="_blank" rel="noopener" @endif
                                        class="flex h-12 w-full items-center gap-3 rounded-xl px-4 text-[15px] font-semibold active:bg-gray-100 {{ $rowTone }}">
                                        <i class="fa-solid {{ $action['icon'] }} w-5 text-center text-sm opacity-70"></i>{{ $action['label'] }}
                                    </a>
                                @else
                                    <button type="button" @click="sheet = false; dialog = '{{ $action['dialog'] }}'"
                                        class="flex h-12 w-full items-center gap-3 rounded-xl px-4 text-left text-[15px] font-semibold active:bg-gray-100 cursor-pointer {{ $rowTone }}">
                                        <i class="fa-solid {{ $action['icon'] }} w-5 text-center text-sm opacity-70"></i>{{ $action['label'] }}
                                    </button>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    <button type="button" @click="sheet = false"
                        class="mt-1 h-12 w-full rounded-xl bg-gray-100 text-[15px] font-bold text-gray-700 cursor-pointer">
                        Cancel
                    </button>
                </div>
            </div>
        @endif
    @endif

    {{-- Dialogs --}}
    @if ($sendUrl)
        <x-document-actions.dialog name="send" :action="$sendUrl" icon="fa-paper-plane" title="Send for review?" confirm="Send for Review" :autofocus-confirm="true">
            <p>The reviewer will be able to pass it, send it back with comments, or fail it. You can't edit the document while it's being reviewed.</p>
        </x-document-actions.dialog>
    @endif

    @if ($reviewUrl)
        <x-document-actions.dialog name="pass" :action="$reviewUrl" icon="fa-check" title="Pass review?" confirm="Pass & Send for Approval" :autofocus-confirm="true">
            <input type="hidden" name="decision" value="pass">
            <p>{{ $label }} goes to the approver. You won't be able to change this decision.</p>
        </x-document-actions.dialog>
    @endif

    @if ($approveUrl)
        <x-document-actions.dialog name="approve" :action="$approveUrl" icon="fa-check-double" title="Approve this document?" confirm="Approve" :autofocus-confirm="true">
            <input type="hidden" name="decision" value="pass">
            <p>{{ $label }} moves to Pending Code. It becomes Active — superseding the current revision — once the Document Controller assigns its code.</p>
        </x-document-actions.dialog>
    @endif

    @if ($rejectUrl)
        <x-document-actions.dialog name="reject" :action="$rejectUrl" icon="fa-xmark" tone="danger" :title="$rejectLabel.($label ? ': '.$label : '')" :confirm="$rejectLabel" :autofocus-confirm="! $rejectNeedsReason">
            <input type="hidden" name="decision" value="fail">
            <p>
                @if ($rejectIsFinal)
                    This is final — the document can no longer be revised or resubmitted.
                @else
                    The process owner can revise the document and send it again.
                @endif
                @if ($commentUrl)
                    To ask for changes instead, use <a href="{{ $commentUrl }}" class="font-semibold text-[#575df9] underline">Comment &amp; Send Back</a>.
                @endif
            </p>
            @if ($rejectNeedsReason)
                <div>
                    <label for="reject-reason" class="block text-sm font-medium text-gray-700">Reason <span class="text-red-500">*</span></label>
                    <textarea id="reject-reason" name="reason" rows="3" maxlength="2000" required data-autofocus
                        class="mt-1 text-sm" placeholder="e.g. Procedure duplicates SP-PUR-02; merge the steps there instead"></textarea>
                    <p class="mt-1 text-xs text-gray-400">The process owner will see this on the document.</p>
                </div>
            @endif
        </x-document-actions.dialog>
    @endif

    @if ($assignCodeUrl)
        <x-document-actions.dialog name="assign" :action="$assignCodeUrl" icon="fa-file-pen" title="Assign document code" confirm="Assign Code & Make Active">
            <p>Pre-filled with the process owner's suggested code and the revision number from past versions.</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-2">
                    <label for="assign-code" class="block text-sm font-medium text-gray-700">Final document code <span class="text-red-500">*</span></label>
                    <input id="assign-code" type="text" name="document_code" value="{{ $suggestedCode }}" maxlength="255" required data-autofocus class="mt-1 text-sm">
                </div>
                <div>
                    <label for="assign-revision" class="block text-sm font-medium text-gray-700">Revision <span class="text-red-500">*</span></label>
                    <input id="assign-revision" type="text" name="revision_number" value="{{ $suggestedRevision ?? '0' }}" maxlength="50" required class="mt-1 text-sm">
                </div>
            </div>
        </x-document-actions.dialog>
    @endif

    @if ($archiveUrl)
        <x-document-actions.dialog name="archive" :action="$archiveUrl" method="DELETE" icon="fa-box-archive" tone="danger" :title="'Archive '.($label ?: 'document').'?'" confirm="Archive Document">
            <p>The document is removed from the list. Your justification is kept in the audit trail.</p>
            <div>
                <label for="archive-justification" class="block text-sm font-medium text-gray-700">Justification <span class="text-red-500">*</span></label>
                <textarea id="archive-justification" name="delete_justification" rows="3" minlength="10" required data-autofocus
                    class="mt-1 text-sm" placeholder="e.g. This procedure will be combined with another procedure"></textarea>
                <p class="mt-1 text-xs text-gray-400">At least 10 characters.</p>
            </div>
        </x-document-actions.dialog>
    @endif
</div>
@endif
