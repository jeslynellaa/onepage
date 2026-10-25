{{-- Shown only inside throwaway demo companies: expiry notice + persona switcher. --}}
@php
  $demoCompany = auth()->user()?->company;
@endphp

@if ($demoCompany?->is_demo)
  @php
    $personas = \App\Models\User::where('company_id', $demoCompany->id)->orderBy('id')->get();
  @endphp
  <div class="bg-[#EEF4FF] border-b border-[#1F6FEB]/20 text-[#0B1020] px-4 py-2.5 mb-2 flex flex-wrap items-center justify-center gap-x-4 gap-y-2 text-[13px]">
    <span class="inline-flex items-center gap-2">
      <i class="fa-solid fa-flask text-[#1F6FEB]"></i>
      <span><strong>Demo workspace</strong> · sample data · deleted {{ $demoCompany->demo_expires_at?->diffForHumans() }}</span>
    </span>

    <form method="POST" action="{{ route('demo.switch') }}" class="inline-flex items-center gap-2">
      @csrf
      <label for="demo-persona" class="font-semibold">Viewing as</label>
      <select id="demo-persona" name="user_id" onchange="this.form.submit()"
        class="rounded-lg border border-[#1F6FEB]/30 bg-white py-1 pl-2 pr-7 text-[13px] focus:border-[#1F6FEB] focus:ring-2 focus:ring-[#1F6FEB]/20">
        @foreach ($personas as $persona)
          <option value="{{ $persona->id }}" @selected($persona->id === auth()->id())>
            {{ $persona->first_name }} — {{ $persona->position }}
          </option>
        @endforeach
      </select>
      <noscript><button type="submit" class="underline font-semibold">Switch</button></noscript>
    </form>

    @if ($blurb = \App\Services\DemoProvisioner::blurbFor(auth()->user()->role))
      <span class="text-[#5B6478]">{{ $blurb }}</span>
    @endif
  </div>
@endif
