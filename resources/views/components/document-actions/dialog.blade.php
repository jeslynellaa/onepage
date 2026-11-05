@props([
    'name',
    'title',
    'action',
    'method' => 'PUT',
    'icon' => 'fa-circle-question',
    'tone' => 'primary',
    'confirm' => 'Confirm',
    'autofocusConfirm' => false, // focus the confirm button on open (when there's no field to fill)
])

@php
    $iconTone = $tone === 'danger' ? 'bg-red-50 text-red-600' : 'bg-[#575df9]/10 text-[#575df9]';
    $confirmTone = $tone === 'danger'
        ? 'bg-red-600 hover:bg-red-700 focus-visible:ring-red-500/40'
        : 'bg-[#575df9] hover:bg-[#464bd4] focus-visible:ring-[#575df9]/40';
@endphp

{{-- Bottom sheet on phones, centered dialog from sm up. Rendered inside <x-document-actions>, which owns `dialog`. --}}
<div x-show="dialog === '{{ $name }}'" x-cloak x-transition.opacity
    x-effect="dialog === '{{ $name }}' && $nextTick(() => $el.querySelector('[data-autofocus]')?.focus())"
    @click.self="dialog = null"
    @keydown.escape.window="dialog = null"
    class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-gray-900/40 sm:p-4"
    role="dialog" aria-modal="true" aria-labelledby="dialog-{{ $name }}-title">
    <form method="POST" action="{{ $action }}"
        @submit="$el.querySelector('[type=submit]').disabled = true"
        class="w-full sm:max-w-md bg-white rounded-t-3xl sm:rounded-2xl shadow-xl px-5 pt-5 sm:p-6 pb-[max(1.25rem,env(safe-area-inset-bottom))] max-h-[90vh] overflow-y-auto">
        @csrf
        @method($method)

        <div class="mx-auto mb-4 h-1 w-10 rounded-full bg-gray-200 sm:hidden" aria-hidden="true"></div>

        <div class="flex items-start gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $iconTone }}">
                <i class="fa-solid {{ $icon }}"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h3 id="dialog-{{ $name }}-title" class="text-base font-bold text-gray-900">{{ $title }}</h3>
                <div class="mt-1 text-sm text-gray-500 space-y-3">
                    {{ $slot }}
                </div>
            </div>
        </div>

        <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
            <button type="button" @click="dialog = null"
                class="h-11 sm:h-10 px-4 rounded-xl text-sm font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 cursor-pointer">
                Cancel
            </button>
            <button type="submit" @if ($autofocusConfirm) data-autofocus @endif
                class="h-11 sm:h-10 px-4 rounded-xl text-sm font-bold text-white cursor-pointer focus-visible:outline-none focus-visible:ring-2 disabled:opacity-60 disabled:cursor-wait {{ $confirmTone }}">
                {{ $confirm }}
            </button>
        </div>
    </form>
</div>
