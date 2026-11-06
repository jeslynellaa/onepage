<x-layout>
    <style>[x-cloak] { display: none !important; }</style>
    @php
        $label = 'block text-[11px] font-bold uppercase text-[#8B93A7] tracking-wide mb-1';
        $input = 'w-full rounded-lg border border-[#D7DBE4] px-3 py-2 text-[14px] focus:outline-none focus:border-[#1F6FEB] focus:ring-2 focus:ring-[#1F6FEB]/20';
        $allIds = $recipients->flatten()->map(fn ($u) => (string) $u->id)->values();
        $searchText = fn ($u) => \Illuminate\Support\Str::lower(trim("{$u->first_name} {$u->last_name} {$u->email} ".($u->company?->name ?? '')));
    @endphp

    <div class="mx-auto w-full px-5 py-1 max-w-[1240px]"
         x-data="{
            selected: @js(array_map('strval', (array) old('recipients', []))),
            search: '',
            subject: @js(old('subject', '')),
            message: @js(old('message', '')),
            buttonLabel: @js(old('button_label', '')),
            buttonUrl: @js(old('button_url', '')),
            matches(text) { return text.includes(this.search.trim().toLowerCase()); },
            allSelected(ids) { return ids.length > 0 && ids.every(id => this.selected.includes(id)); },
            someSelected(ids) { return ids.some(id => this.selected.includes(id)); },
            toggleGroup(ids) {
                this.selected = this.allSelected(ids)
                    ? this.selected.filter(id => !ids.includes(id))
                    : [...new Set([...this.selected, ...ids])];
            },
            selectVisible() {
                const visible = [...this.$root.querySelectorAll('[data-recipient]')]
                    .filter(el => this.matches(el.dataset.search)).map(el => el.dataset.recipient);
                this.selected = [...new Set([...this.selected, ...visible])];
            },
            get paragraphs() { return this.message.trim() ? this.message.trim().split(/\n\s*\n/) : []; },
         }">

        <!-- Header -->
        <div class="mb-4">
            <a href="{{ route('admin.index') }}" class="text-[13px] text-[#8B93A7] hover:text-[#1F6FEB]"><i class="fa-solid fa-arrow-left mr-1"></i> Admin</a>
            <h1 class="font-semibold text-gray-800 mt-1">Email Users</h1>
            <p class="text-[13px] text-[#5B6478] mt-1">Write a message and choose who receives it. Each person gets their own copy addressed to them by first name, so recipients never see each other. Replies go to {{ \App\Mail\DemoInvitationMail::CONTACT_EMAIL }}.</p>
        </div>

        <form method="POST" action="{{ route('admin.emails.send') }}"
              @submit="if ($event.submitter?.name !== 'test' && !confirm(`Send this email to ${selected.length} ${selected.length === 1 ? 'recipient' : 'recipients'}?`)) $event.preventDefault()"
              class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_380px] gap-5 items-start mb-8">
            @csrf

            <div class="space-y-5 min-w-0">
                <!-- Compose -->
                <div class="bg-white rounded-2xl shadow-lg p-5">
                    <div class="font-semibold mb-3">Message</div>

                    <div class="space-y-4">
                        <div>
                            <label for="subject" class="{{ $label }}">Subject</label>
                            <input id="subject" name="subject" x-model="subject" required maxlength="200"
                                   placeholder="e.g. Scheduled maintenance on Friday, 1:00–4:00 PM" class="{{ $input }}">
                        </div>

                        <div>
                            <label for="message" class="{{ $label }}">Message</label>
                            <textarea id="message" name="message" x-model="message" required maxlength="10000" rows="9"
                                      placeholder="Write your message here. Leave a blank line between paragraphs."
                                      class="{{ $input }} leading-relaxed"></textarea>
                            <p class="text-[12px] text-[#8B93A7] mt-1">The greeting ("Hi [first name],") and sign-off are added for you.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-[220px_minmax(0,1fr)] gap-4">
                            <div>
                                <label for="button_label" class="{{ $label }}">Button text <span class="normal-case font-normal">(optional)</span></label>
                                <input id="button_label" name="button_label" x-model="buttonLabel" maxlength="60"
                                       placeholder="e.g. Open OnePage" class="{{ $input }}">
                            </div>
                            <div>
                                <label for="button_url" class="{{ $label }}">Button link <span class="normal-case font-normal">(optional)</span></label>
                                <input id="button_url" name="button_url" type="url" x-model="buttonUrl" maxlength="2048"
                                       placeholder="{{ url('/login') }}" class="{{ $input }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preview -->
                <div class="bg-white rounded-2xl shadow-lg p-5">
                    <div class="font-semibold mb-3">Preview</div>

                    <div class="bg-[#F5F7FA] rounded-xl px-4 py-7 sm:px-8">
                        <div class="max-w-[600px] mx-auto">
                            <img src="{{ asset('onepage-name.png') }}" alt="OnePage" class="h-[26px] w-auto mb-5 ml-1">
                            <div class="bg-white rounded-2xl border-t-4 border-[#1F6FEB] px-7 py-8 sm:px-9 font-[Arial,Helvetica,sans-serif]">
                                <h2 class="m-0 mb-5 text-[22px] leading-snug font-bold text-[#0B1020] break-words" x-text="subject || 'Your subject'"></h2>
                                <p class="m-0 mb-4 text-[15px] leading-[1.65] text-[#0B1020]">Hi [first name],</p>
                                <template x-for="(p, i) in paragraphs" :key="i">
                                    <p class="m-0 mb-4 text-[15px] leading-[1.65] text-[#3A4256] whitespace-pre-line break-words" x-text="p"></p>
                                </template>
                                <p x-show="paragraphs.length === 0" class="m-0 mb-4 text-[15px] leading-[1.65] text-[#B4BAC8]">Your message appears here.</p>
                                <div x-show="buttonLabel && buttonUrl" x-cloak class="mt-7 mb-2">
                                    <span class="inline-block bg-[#1F6FEB] text-white text-[15px] font-bold px-7 py-[13px] rounded-full" x-text="buttonLabel"></span>
                                </div>
                                <p class="m-0 mt-7 text-[15px] leading-[1.65] text-[#3A4256]">Best regards,<br><strong class="text-[#0B1020]">The OnePage Team</strong></p>
                            </div>
                            <p class="text-center text-[12px] leading-relaxed text-[#8B93A7] mt-6 mb-0">
                                You're receiving this because you have a OnePage account.<br>
                                Questions? Just reply to this email.<br>
                                OnePage by FCU
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recipients -->
            <div class="bg-white rounded-2xl shadow-lg p-5 lg:sticky lg:top-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="font-semibold">Recipients</div>
                    <span class="text-[13px] font-semibold text-[#1F6FEB]" x-text="`${selected.length} of {{ $allIds->count() }} selected`"></span>
                </div>

                <label for="recipient-search" class="sr-only">Search recipients</label>
                <div class="relative mb-2">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-[12px] text-[#8B93A7]"></i>
                    <input id="recipient-search" type="search" x-model="search" placeholder="Search name, email or company"
                           @keydown.enter.prevent
                           class="{{ $input }} pl-8!">
                </div>

                <div class="flex items-center gap-3 text-[13px] mb-3">
                    <button type="button" @click="selectVisible()" class="text-[#1F6FEB] font-semibold hover:underline cursor-pointer"
                            x-text="search.trim() ? 'Select matching' : 'Select all'">Select all</button>
                    <span class="text-[#D7DBE4]">|</span>
                    <button type="button" @click="selected = []" class="text-[#5B6478] font-semibold hover:underline cursor-pointer">Clear</button>
                </div>

                <div class="max-h-[460px] overflow-y-auto -mx-2 px-2 border-t border-[#E7EAF0]">
                    @forelse ($recipients as $company => $users)
                        @php
                            $groupIds = $users->map(fn ($u) => (string) $u->id)->values();
                            $groupSearch = $users->map($searchText)->values();
                        @endphp
                        <div x-show="@js($groupSearch).some(s => matches(s))" class="py-2 border-b border-[#E7EAF0] last:border-b-0">
                            <label class="flex items-center gap-2 py-1.5 cursor-pointer">
                                <input type="checkbox" class="w-4! h-4! p-0! shrink-0 accent-[#1F6FEB] cursor-pointer"
                                       aria-label="Select everyone at {{ $company }}"
                                       x-effect="$el.checked = allSelected(@js($groupIds)); $el.indeterminate = !$el.checked && someSelected(@js($groupIds))"
                                       @change="toggleGroup(@js($groupIds))">
                                <span class="text-[12px] font-bold uppercase tracking-wide text-[#0B1020]">{{ $company }}</span>
                                <span class="text-[12px] text-[#8B93A7]">({{ $users->count() }})</span>
                            </label>

                            @foreach ($users as $user)
                                <label data-recipient="{{ $user->id }}" data-search="{{ $searchText($user) }}"
                                       x-show="matches($el.dataset.search)"
                                       class="flex items-start gap-2 pl-6 py-1.5 rounded-lg hover:bg-[#F5F7FA] cursor-pointer">
                                    <input type="checkbox" name="recipients[]" value="{{ $user->id }}" x-model="selected"
                                           class="w-4! h-4! p-0! mt-0.5 shrink-0 accent-[#1F6FEB] cursor-pointer">
                                    <span class="min-w-0 text-[13px] leading-tight">
                                        <span class="block font-semibold text-[#0B1020] truncate">
                                            {{ trim("{$user->first_name} {$user->last_name}") ?: $user->username }}
                                            @unless ($user->active)
                                                <span class="ml-1 px-1.5 py-px rounded-full bg-gray-100 text-gray-600 text-[11px] font-semibold align-middle">Inactive</span>
                                            @endunless
                                        </span>
                                        <span class="block text-[#5B6478] truncate">{{ $user->email }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    @empty
                        <p class="py-4 text-[13px] text-[#8B93A7]">No users with an email address yet.</p>
                    @endforelse
                </div>

                <div class="border-t border-[#E7EAF0] pt-4 mt-1 space-y-2">
                    <button type="submit" :disabled="selected.length === 0"
                            class="w-full inline-flex items-center justify-center gap-2 bg-[#1F6FEB] hover:bg-[#1A5FD0] disabled:bg-[#B4BAC8] disabled:cursor-not-allowed text-white font-semibold px-4 py-2.5 rounded-lg cursor-pointer duration-200">
                        <i class="fa-regular fa-paper-plane"></i>
                        <span x-text="selected.length ? `Send to ${selected.length} ${selected.length === 1 ? 'recipient' : 'recipients'}` : 'Select recipients to send'">Send</span>
                    </button>
                    <button type="submit" name="test" value="1"
                            class="w-full border border-[#D7DBE4] hover:border-[#1F6FEB] hover:text-[#1F6FEB] text-[#0B1020] font-semibold px-4 py-2 rounded-lg cursor-pointer duration-200 text-[14px]">
                        Send test to me ({{ auth()->user()->email }})
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-layout>
