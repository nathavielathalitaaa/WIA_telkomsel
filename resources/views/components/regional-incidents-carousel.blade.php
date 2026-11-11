@props(['incidents', 'title' => 'Pemantauan Saat Ini', 'showAllUrl' => null])

@php
    $incidentsCollection = collect($incidents ?? []);
    $allUrl = $showAllUrl ?: route('public.monitor');
    $carouselId = 'regional-carousel-' . \Illuminate\Support\Str::uuid();
@endphp

<section id="{{ $carouselId }}" aria-label="Pemantauan Saat Ini" class="space-y-2 sm:space-y-2.5">
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-2 min-w-0">
            <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
            <h2 class="font-telemetry text-xs sm:text-sm font-bold uppercase tracking-wider text-[#202938]">
                {{ $title }}
            </h2>
        </div>

        <a href="{{ $allUrl }}"
           class="text-xs font-telemetry font-bold link-text shrink-0">
            Lihat semua
        </a>
    </div>

    @if($incidentsCollection->isEmpty())
        <div class="bg-[#FEFCE8] border border-[#EAB308] rounded-xl p-4 sm:p-5 text-center text-xs text-amber-900">
            <p class="font-bold text-amber-950">Belum ada kejadian yang tercatat.</p>
            <p class="mt-1 text-amber-800">Informasi kejadian akan ditampilkan secara otomatis di sini.</p>
        </div>
    @else
        <div class="relative overflow-hidden rounded-xl"
             role="region"
             aria-roledescription="carousel"
             aria-label="Pemantauan saat ini">

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

                        // Seluruh card pemantauan saat ini selalu berwarna kuning (#FEFCE8 dengan border #EAB308)
                        $cardBg = 'bg-[#FEFCE8] border-[#EAB308]';
                        $badgeBg = 'bg-[#FEF08A] text-[#854D0E] border-[#EAB308]';
                        $ctaBtn = 'bg-[#173B70] hover:bg-[#0F2A52]';
                    @endphp

                    <article class="w-full shrink-0 min-w-0 {{ $cardBg }} border rounded-xl p-3 sm:p-3.5 flex flex-col justify-between gap-2"
                             aria-label="Kejadian {{ $index + 1 }} dari {{ $incidentsCollection->count() }}"
                             aria-roledescription="slide">

                        <!-- Baris 1: Status Tingkat, Sumber, Wilayah, & Waktu Kejadian -->
                        <div class="flex items-center justify-between gap-2 min-w-0">
                            <div class="flex items-center gap-1.5 sm:gap-2 min-w-0">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider shrink-0 {{ $badgeBg }}">
                                    {{ $isUrgent ? 'Peringatan: ' : 'Situasi: ' }}{{ strtoupper($sevVal ?: 'TERCATAT') }}
                                </span>

                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase border shrink-0
                                    {{ $inc->is_simulation ? 'bg-white/90 text-slate-700 border-amber-300' : 'bg-[#173B70] text-white border-[#173B70]' }}">
                                    {{ $inc->is_simulation ? 'Simulasi' : 'Historis BNPB' }}
                                </span>

                                <span class="font-telemetry text-xs font-semibold text-[#173B70] truncate">
                                    {{ $regionName }}
                                </span>
                            </div>

                            <span class="text-[10px] sm:text-xs text-slate-600 font-telemetry shrink-0 whitespace-nowrap">
                                {{ $timeStr }}
                            </span>
                        </div>

                        <!-- Baris 2: Judul Kejadian -->
                        <h3 class="text-sm sm:text-base font-bold text-[#202938] truncate leading-tight">
                            <a href="{{ route('public.incidents.show', $inc->slug) }}" class="hover:text-[#173B70] hover:underline">
                                {{ $inc->title }}
                            </a>
                        </h3>

                        <!-- Baris 3: Metadata Status & Tombol Tindak Lanjut / Detail -->
                        <div class="flex items-center justify-between gap-2 pt-1 border-t border-amber-300/80 min-w-0">
                            <div class="flex items-center gap-1.5 sm:gap-2 text-[11px] sm:text-xs text-slate-700 truncate min-w-0">
                                <span class="font-semibold text-slate-900 shrink-0">{{ $typeTitle }}</span>
                                <span class="text-amber-400">·</span>
                                <span class="truncate font-medium text-amber-950">{{ $verifLabel }}</span>
                                <span class="text-amber-400 hidden xs:inline">·</span>
                                <span class="text-slate-700 truncate hidden xs:inline">{{ $followLabel }}</span>
                            </div>

                            <a href="{{ route('public.incidents.show', $inc->slug) }}"
                               class="inline-flex items-center justify-center px-3 py-1 sm:px-3.5 sm:py-1 rounded-md {{ $ctaBtn }} text-white text-[11px] sm:text-xs font-bold uppercase tracking-wider shrink-0 transition shadow-xs">
                                Lihat kejadian
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            @if($incidentsCollection->count() > 1)
                <div class="flex items-center justify-between gap-3 mt-2">
                    <div class="flex items-center gap-1.5 regional-carousel-dots" aria-label="Pilih kejadian">
                        @foreach($incidentsCollection as $index => $inc)
                            <button type="button"
                                    class="regional-carousel-dot h-1.5 rounded-full transition-all duration-300 {{ $index === 0 ? 'w-6 bg-[#173B70]' : 'w-1.5 bg-amber-300' }}"
                                    data-slide="{{ $index }}"
                                    aria-label="Tampilkan kejadian {{ $index + 1 }}"
                                    aria-current="{{ $index === 0 ? 'true' : 'false' }}">
                            </button>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button"
                                class="regional-carousel-prev w-7 h-7 rounded-lg border border-amber-300 bg-white hover:bg-amber-50 text-slate-700 flex items-center justify-center"
                                aria-label="Kejadian sebelumnya">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>

                        <button type="button"
                                class="regional-carousel-next w-7 h-7 rounded-lg border border-amber-300 bg-white hover:bg-amber-50 text-slate-700 flex items-center justify-center"
                                aria-label="Kejadian berikutnya">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

    let reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    try {
        window.matchMedia('(prefers-reduced-motion: reduce)').addEventListener('change', (e) => {
            reducedMotion = e.matches;
            if (reducedMotion) {
                stopTimer();
                if (resumeTimeout) window.clearTimeout(resumeTimeout);
            } else {
                startTimer();
            }
        });
    } catch (_) {}

    let current = 0;
    let timer = null;
    let resumeTimeout = null;

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
        if (reducedMotion || document.hidden) return;
        timer = window.setInterval(() => showSlide(current + 1), 3500);
    }

    function pauseAndResumeAfter(delay = 2000) {
        stopTimer();
        if (resumeTimeout) window.clearTimeout(resumeTimeout);
        if (reducedMotion) return;
        resumeTimeout = window.setTimeout(() => {
            startTimer();
        }, delay);
    }

    prev?.addEventListener('click', () => {
        showSlide(current - 1);
        pauseAndResumeAfter(2000);
    });

    next?.addEventListener('click', () => {
        showSlide(current + 1);
        pauseAndResumeAfter(2000);
    });

    dots.forEach((dot) => {
        dot.addEventListener('click', () => {
            showSlide(Number(dot.dataset.slide));
            pauseAndResumeAfter(2000);
        });
    });

    // Berhenti saat hover, lanjut setelah 2 detik
    root.addEventListener('mouseenter', () => pauseAndResumeAfter(2000));
    root.addEventListener('mouseleave', () => pauseAndResumeAfter(2000));

    // Berhenti saat fokus, lanjut setelah 2 detik
    root.addEventListener('focusin', () => pauseAndResumeAfter(2000));
    root.addEventListener('focusout', (event) => {
        if (!root.contains(event.relatedTarget)) {
            pauseAndResumeAfter(2000);
        }
    });

    // Berhenti saat sentuh, lanjut setelah 2 detik
    root.addEventListener('touchstart', () => pauseAndResumeAfter(2000), { passive: true });
    root.addEventListener('touchend', () => pauseAndResumeAfter(2000), { passive: true });
    root.addEventListener('touchcancel', () => pauseAndResumeAfter(2000), { passive: true });

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stopTimer();
            if (resumeTimeout) window.clearTimeout(resumeTimeout);
        } else if (!reducedMotion) {
            startTimer();
        }
    });

    showSlide(0);
    if (!reducedMotion) {
        startTimer();
    }
})();
</script>
@endif
