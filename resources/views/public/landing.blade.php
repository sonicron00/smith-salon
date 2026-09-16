@extends('layouts.public')

@section('content')
<div class="space-y-6 sm:space-y-10">
    <section class="overflow-hidden rounded-[2rem] bg-gradient-to-b from-[#f4e8e8] via-[#e7c3c3] to-[#d8abab] text-[#4d302f] shadow-xl">
        <div class="grid gap-6 px-6 py-8 sm:px-10 sm:py-12 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
            <div>
                <p class="text-2xl font-semibold uppercase tracking-[0.35em] text-[#9a6f6f]">Luxury beauty & head spa</p>
                <h1 class="mt-4 text-sm font-semibold leading-tight sm:text-sm text-[#4d302f]">Head spa, lashes and brows in a calm, relaxing space.</h1>
                <p class="mt-4 max-w-2xl text-sm leading-7 text-[#6f4d4d] sm:text-base">
                    Treat yourself to elevated head spa treatments and beautifully tailored lash and brow appointments.
                    Mobile-friendly booking and a warm boutique feel.
                </p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('booking.start') }}" class="inline-flex items-center rounded-full bg-white px-5 py-3 text-sm font-semibold text-stone-900 transition hover:bg-rose-50">Start booking</a>
                    <a href="{{ route('about') }}" class="inline-flex items-center rounded-full bg-white px-5 py-3 text-sm font-semibold text-stone-900 transition hover:bg-rose-50">About us</a>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
                <div class="rounded-[1.75rem] bg-white/45 p-5 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.25em] text-[#9a6f6f]">Signature treatments</p>
                    <ul class="mt-4 space-y-3 text-sm text-[#5f4141]">
                        <li>• Luxury head spa rituals</li>
                        <li>• Lash lift and lash enhancement</li>
                        <li>• Brow shaping and tinting</li>

                    </ul>
                </div>
                <div class="rounded-[1.75rem] border border-white/40 bg-white/30 p-6 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.25em] text-[#9a6f6f]">A boutique experience</p>
                    <p class="mt-3 text-sm leading-7 text-[#5f4141]">
                        Calm, relaxing treatments with a luxury feel from your booking to aftercare.
                    </p>
                </div>
            </div>
        </div>
    </section>
<!-- 
    <section class="grid gap-4 sm:grid-cols-2">
        <div class="rounded-[1.5rem] border border-rose-100 bg-white p-5 shadow-sm">
            <div class="text-sm font-semibold text-stone-900">Choose a treatment</div>
            <p class="mt-2 text-sm leading-6 text-stone-600">Pick from head spa, lash and brow services.</p>
        </div>
        <div class="rounded-[1.5rem] border border-rose-100 bg-white p-5 shadow-sm">
            <div class="text-sm font-semibold text-stone-900">Pick your therapist</div>
            <p class="mt-2 text-sm leading-6 text-stone-600">Book directly with the owner or team member you want.</p>
        </div>
       <div class="rounded-[1.5rem] border border-rose-100 bg-white p-5 shadow-sm">
            <div class="text-sm font-semibold text-stone-900">Manage by text link</div>
            <p class="mt-2 text-sm leading-6 text-stone-600">Clients can reschedule or cancel from their booking link without calling.</p>
        </div> -->
    </section>

    @if (!empty($googleReviews) && !empty($googleReviews['reviews']))
        <section class="rounded-[1.75rem] border border-rose-100 bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-2xl font-semibold text-stone-900">What our clients say</h2>
                @if ($googleReviews['rating'])
                    <div class="flex items-center gap-2 text-sm text-stone-600">
                        <span class="text-lg font-semibold text-stone-900">{{ number_format($googleReviews['rating'], 1) }}</span>
                        <span class="text-amber-500">
                            @for ($i = 1; $i <= 5; $i++)
                                {{ $i <= round($googleReviews['rating']) ? '★' : '☆' }}
                            @endfor
                        </span>
                        <span>({{ $googleReviews['total'] }} Google reviews)</span>
                    </div>
                @endif
            </div>

            <div
                x-data="reviewCarousel()"
                x-init="init()"
                class="relative mt-4"
            >
                <!-- Prev button -->
                <button
                    type="button"
                    @click="prev()"
                    class="absolute -left-2 top-1/2 z-10 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-stone-800/80 text-white shadow transition hover:bg-stone-900 sm:-left-4"
                    aria-label="Previous reviews"
                >
                    ‹
                </button>

                <!-- Track -->
                <div class="overflow-hidden" x-ref="viewport">
                    <div
                        class="flex ease-out"
                        :class="fast ? 'transition-transform duration-150' : 'transition-transform duration-500'"
                        :style="`transform: translateX(-${offset}px)`"
                        x-ref="track"
                        @touchstart="onTouchStart($event)"
                        @touchend="onTouchEnd($event)"
                    >
                        @foreach ($googleReviews['reviews'] as $index => $review)
                            <div class="w-full shrink-0 px-2 sm:w-1/2 lg:w-1/3">
                                <div class="flex h-full flex-col rounded-[1.25rem] border border-rose-100 bg-stone-50 p-3">
                                    <div class="flex items-center gap-2">
                                        @if ($review['author_photo'])
                                            <img src="{{ $review['author_photo'] }}" alt="{{ $review['author'] }}" class="h-7 w-7 rounded-full" referrerpolicy="no-referrer" />
                                        @else
                                            <div class="flex h-7 w-7 items-center justify-center rounded-full bg-rose-100 text-xs font-semibold text-rose-700">
                                                {{ strtoupper(substr($review['author'], 0, 1)) }}
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <div class="truncate text-xs font-semibold text-stone-900">{{ $review['author'] }}</div>
                                            <div class="text-[10px] text-amber-500">
                                                @for ($i = 1; $i <= 5; $i++){{ $i <= $review['rating'] ? '★' : '☆' }}@endfor
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mt-2 line-clamp-3 text-xs leading-5 text-stone-700">{{ $review['text'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Next button -->
                <button
                    type="button"
                    @click="next()"
                    class="absolute -right-2 top-1/2 z-10 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-stone-800/80 text-white shadow transition hover:bg-stone-900 sm:-right-4"
                    aria-label="Next reviews"
                >
                    ›
                </button>

                <!-- Dots -->
                <div class="mt-4 flex justify-center gap-2">
                    <template x-for="i in pageCount" :key="i">
                        <button
                            type="button"
                            @click="goToPage(i - 1)"
                            class="h-2 w-2 rounded-full transition"
                            :class="page === (i - 1) ? 'bg-rose-500 w-5' : 'bg-stone-300'"
                            :aria-label="`Go to page ${i}`"
                        ></button>
                    </template>
                </div>
            </div>

            <script>
                function reviewCarousel() {
                    return {
                        index: 0,
                        page: 0,
                        perView: 3,
                        total: {{ count($googleReviews['reviews']) }},
                        offset: 0,
                        cardWidth: 0,
                        touchStartX: 0,
                        pageCount: 1,
                        fast: false,

                        init() {
                            this.calc();
                            window.addEventListener('resize', () => this.calc());
                        },
                        calc() {
                            const w = window.innerWidth;
                            this.perView = w < 640 ? 1 : (w < 1024 ? 2 : 3);
                            const track = this.$refs.track;
                            const firstCard = track ? track.children[0] : null;
                            this.cardWidth = firstCard ? firstCard.getBoundingClientRect().width : 0;
                            this.pageCount = Math.max(1, Math.ceil(this.total / this.perView));
                            this.clamp();
                            this.apply();
                        },
                        maxIndex() {
                            return Math.max(0, this.total - this.perView);
                        },
                        clamp() {
                            if (this.index > this.maxIndex()) this.index = this.maxIndex();
                            if (this.index < 0) this.index = 0;
                        },
                        apply() {
                            this.offset = this.index * this.cardWidth;
                            this.page = Math.floor(this.index / this.perView);
                        },
                        next() {
                            if (this.index >= this.maxIndex()) {
                                this.snapTo(0);
                            } else {
                                this.index += this.perView;
                                this.clamp();
                                this.apply();
                            }
                        },
                        prev() {
                            if (this.index <= 0) {
                                this.snapTo(this.maxIndex());
                            } else {
                                this.index -= this.perView;
                                this.clamp();
                                this.apply();
                            }
                        },
                        // Quick rollback: use a faster transition when jumping across the whole track
                        snapTo(target) {
                            this.fast = true;
                            this.index = target;
                            this.apply();
                            setTimeout(() => { this.fast = false; }, 250);
                        },
                        goToPage(p) {
                            this.index = p * this.perView;
                            this.clamp();
                            this.apply();
                        },
                        onTouchStart(e) { this.touchStartX = e.changedTouches[0].screenX; },
                        onTouchEnd(e) {
                            const diff = e.changedTouches[0].screenX - this.touchStartX;
                            if (Math.abs(diff) < 40) return;
                            diff < 0 ? this.next() : this.prev();
                        },
                    };
                }
            </script>

            @if (!empty($googleReviews['maps_uri']) && $googleReviews['total'] > count($googleReviews['reviews']))
                <div class="mt-6 text-center">
                    <a
                        href="{{ $googleReviews['maps_uri'] }}"
                        target="_blank"
                        rel="noopener"
                        class="inline-flex items-center gap-2 rounded-full border border-rose-200 px-5 py-2 text-sm font-medium text-rose-800 transition hover:bg-rose-50"
                    >
                        Read all {{ $googleReviews['total'] }} reviews on Google
                    </a>
                </div>
            @endif
        </section>
    @endif

    <section class="grid gap-6 lg:grid-cols-[1fr_0.9fr]">
        <div class="rounded-[1.75rem] border border-rose-100 bg-white p-6 shadow-sm">
            <h2 class="text-2xl font-semibold text-stone-900">Visit us</h2>
            <p class="mt-3 text-sm leading-7 text-stone-600">{{ config('salon.address') }}</p>
            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode(config('salon.address')) }}" target="_blank" rel="noopener" class="mt-4 inline-flex rounded-full border border-rose-200 px-4 py-2 text-sm font-medium text-rose-800 transition hover:bg-rose-50">Open in Google Maps</a>

            <div class="mt-6 grid gap-2 text-sm text-stone-600">
                @foreach (config('salon.opening_hours') as $line)
                    <div class="flex items-center justify-between rounded-xl bg-stone-50 px-4 py-3">
                        <span>{{ $line['day'] }}</span>
                        <span class="font-medium text-stone-900">{{ $line['hours'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-[1.75rem] border border-rose-100 bg-white p-3 shadow-sm">
            <iframe
                title="Salon map"
                class="h-[320px] w-full rounded-[1.25rem] border-0"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                src="https://www.google.com/maps?q={{ urlencode(config('salon.address')) }}&z=15&output=embed">
            </iframe>
        </div>
    </section>
</div>
@endsection
