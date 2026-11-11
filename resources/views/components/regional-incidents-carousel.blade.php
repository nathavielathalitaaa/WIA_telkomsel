@props(['incidents', 'title' => 'Kejadian Berdasarkan Wilayah', 'showAllUrl' => null])

@php
    $incidentsCollection = collect($incidents ?? []);
    $allUrl = $showAllUrl ?: route('public.monitor');
    $carouselId = 'regional-carousel-' . \Illuminate\Support\Str::uuid();
@endphp

<section id="{{ $carouselId }}" aria-label="Kejadian Bencana Berdasarkan Wilayah" class="space-y-3">
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-2 min-w-0">
            <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
            <h2 class="font-telemetry text-xs sm:text-sm font-bold uppercase tracking-wider text-[#202938]">
                {{ $title }}
            </h2>
        </div>

        <a href="{{ $allUrl }}"
           class="text-xs font-telemetry font-bold text-[#173B70] hover:underline inline-flex items-center gap-1 shrink-0">
            Lihat Semua <span aria-hidden="true">&rarr;</span>
        </a>
    </div>

    @if($incidentsCollection->isEmpty())
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-6 text-center text-xs text-slate-500">
            <p class="font-medium text-slate-700">Belum ada kejadian yang tercatat.</p>
            <p class="mt-1 text-slate-400">Informasi kejadian akan ditampilkan di sini.</p>
        </div>
    @else
        <div class="relative overflow-hidden rounded-xl"
             role="region"
             aria-roledescription="carousel"
             aria-label="Kejadian berdasarkan wilayah">

            <div class="regional-carousel-track flex transition-transform duration-500 ease-in-out motion-reduce:transition-none">
                @foreach($incidentsCollection as $index => $inc)
                    @php
                        $sevVal = $inc->severity instanceof \App\Enums\IncidentSeverity
                            ? $inc->severity->value
                            : (string) $inc->severity;

                        $isUrgent = in_array($sevVal, ['kritis', 'tinggi']);
                        $regionName = $inc->wilayahAdministratif?->nama ?: $inc->location_name;
                        $typeTitle = ucwords(str_replace('_', ' ', $inc->incident_type));
                        $timeStr = $inc->occurred_at
                            ? $inc->occurred_at->format('d M Y, H:i') . ' WIB'
                            : '-';

                        $verifLabel = $inc->verification_status instanceof \App\Enums\VerificationStatus
                            ? $inc->verification_status->label()
                            : (string) $inc->verification_status;

                        $followLabel = $inc->follow_up_status instanceof \App\Enums\FollowUpStatus
                            ? $inc->follow_up_status->label()
                            : (string) $inc->follow_up_status;

                        $cardBg = $isUrgent
                            ? 'bg-[#FEFCE8] border-[#EAB308]'
                            : 'bg-white border-slate-200';

                        $badgeBg = $isUrgent
                            ? 'bg-[#FEF08A] text-[#854D0E] border-[#EAB308]'
                            : 'bg-slate-100 text-slate-700 border-slate-200';

                        $ctaBtn = $isUrgent
                            ? 'bg-[#CA8A04] hover:bg-[#A16207]'
                            : 'bg-[#173B70] hover:bg-[#0F2A52]';
                    @endphp

                    <article class="w-full shrink-0 min-w-0 {{ $cardBg }} border rounded-xl p-5 sm:p-6 flex flex-col justify-between gap-5"
                             aria-label="Kejadian {{ $index + 1 }} dari {{ $incidentsCollection->count() }}"
                             aria-roledescription="slide">

                        <div class="space-y-4">
                            <div class="flex items-center justify-between gap-3">
                                <span class="px-3 py-1 rounded-md border text-[10px] sm:text-xs font-bold uppercase tracking-wider {{ $badgeBg }}">
                                    {{ $isUrgent ? 'Peringatan: ' : 'Situasi: ' }}{{ strtoupper($sevVal ?: 'TERCATAT') }}
                                </span>

                                <span class="px-2 py-1 rounded text-[9px] font-bold uppercase border
                                    {{ $inc->is_simulation ? 'bg-slate-200 text-slate-700 border-slate-300' : 'bg-[#173B70] text-white border-[#173B70]' }}">
                                    {{ $inc->is_simulation ? 'Simulasi' : 'Historis BNPB' }}
                                </span>
                            </div>

                            <div class="space-y-2">
                                <p class="font-telemetry text-xs sm:text-sm font-bold text-[#173B70] uppercase tracking-wide">
                                    <span aria-hidden="true">📍</span> {{ $regionName }}
                                </p>

                                <h3 class="text-lg sm:text-2xl font-bold text-[#202938] leading-snug">
                                    {{ $inc->title }}
                                </h3>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-4 border-t border-slate-200/80">
                                <div>
                                    <p class="text-[10px] uppercase tracking-wider text-slate-400">Jenis kejadian</p>
                                    <p class="text-xs sm:text-sm font-semibold text-slate-700 mt-1">{{ $typeTitle }}</p>
                                </div>

                                <div>
                                    <p class="text-[10px] uppercase tracking-wider text-slate-400">Waktu kejadian</p>
                                    <p class="text-xs sm:text-sm font-semibold text-slate-700 mt-1">{{ $timeStr }}</p>
                                </div>

                                <div>
                                    <p class="text-[10px] uppercase tracking-wider text-slate-400">Status</p>
                                    <p class="text-xs sm:text-sm font-semibold text-slate-700 mt-1">
                                        {{ $verifLabel }} · {{ $followLabel }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-3 border-t border-slate-200/80">
                            <p class="text-[10px] text-slate-500">
                                Kejadian {{ $index + 1 }} dari {{ $incidentsCollection->count() }}
                            </p>

                            <a href="{{ route('public.incidents.show', $inc->slug) }}"
                               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg {{ $ctaBtn }} text-white text-xs font-bold uppercase tracking-wider transition">
                                Lihat Kejadian <span aria-hidden="true">&rarr;</span>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            @if($incidentsCollection->count() > 1)
                <div class="flex items-center justify-between gap-3 mt-3">
                    <div class="flex items-center gap-1.5 regional-carousel-dots" aria-label="Pilih kejadian">
                        @foreach($incidentsCollection as $index => $inc)
                            <button type="button"
                                    class="regional-carousel-dot h-1.5 rounded-full transition-all duration-300 {{ $index === 0 ? 'w-6 bg-[#173B70]' : 'w-1.5 bg-slate-300' }}"
                                    data-slide="{{ $index }}"
                                    aria-label="Tampilkan kejadian {{ $index + 1 }}"
                                    aria-current="{{ $index === 0 ? 'true' : 'false' }}">
                            </button>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button"
                                class="regional-carousel-prev w-8 h-8 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 flex items-center justify-center"
                                aria-label="Kejadian sebelumnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>

                        <button type="button"
                                class="regional-carousel-next w-8 h-8 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 flex items-center justify-center"
                                aria-label="Kejadian berikutnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    @endif
</section>

@if($incidentsCollection->count() > 1)
<script>
(() => {
    const root = document.getElementById(@json($carouselId));
    if (!root || root.dataset.initialized === 'true') return;
    root.dataset.initialized = 'true';

    const track = root.querySelector('.regional-carousel-track');
    const slides = Array.from(track.children);
    const dots = Array.from(root.querySelectorAll('.regional-carousel-dot'));
    const prev = root.querySelector('.regional-carousel-prev');
    const next = root.querySelector('.regional-carousel-next');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    let current = 0;
    let timer = null;
    let paused = false;

    function showSlide(index) {
        current = (index + slides.length) % slides.length;
        track.style.transform = `translateX(-${current * 100}%)`;

        dots.forEach((dot, i) => {
            const active = i === current;
            dot.classList.toggle('w-6', active);
            dot.classList.toggle('w-1.5', !active);
            dot.classList.toggle('bg-[#173B70]', active);
            dot.classList.toggle('bg-slate-300', !active);
            dot.setAttribute('aria-current', active ? 'true' : 'false');
        });
    }

    function stopTimer() {
        if (timer) window.clearInterval(timer);
        timer = null;
    }

    function startTimer() {
        stopTimer();
        if (paused || reducedMotion || document.hidden) return;
        timer = window.setInterval(() => showSlide(current + 1), 6000);
    }

    prev?.addEventListener('click', () => {
        showSlide(current - 1);
        startTimer();
    });

    next?.addEventListener('click', () => {
        showSlide(current + 1);
        startTimer();
    });

    dots.forEach((dot) => {
        dot.addEventListener('click', () => {
            showSlide(Number(dot.dataset.slide));
            startTimer();
        });
    });

    root.addEventListener('mouseenter', () => {
        paused = true;
        stopTimer();
    });

    root.addEventListener('mouseleave', () => {
        paused = false;
        startTimer();
    });

    root.addEventListener('focusin', () => {
        paused = true;
        stopTimer();
    });

    root.addEventListener('focusout', (event) => {
        if (!root.contains(event.relatedTarget)) {
            paused = false;
            startTimer();
        }
    });

    document.addEventListener('visibilitychange', startTimer);

    showSlide(0);
    startTimer();
})();
</script>
@endif
