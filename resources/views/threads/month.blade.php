@extends('layout')

@php
    $date = $ofMonth->toMutable()->firstOfMonth();

    if ($date->dayOfWeek !== 0) {
        $date->subDays($date->dayOfWeek);
    }
@endphp

@section('content')
    <div class="mb-3 flex justify-between text-xl font-bold text-gray-600 dark:text-gray-300 md:mb-6 md:text-3xl">
        <a class="p-2" href="{{ route('threads.month', ['month' => $ofMonth->subMonth()->format('Y-m')]) }}">
            &#10094;
        </a>
        <h1 class="p-2">{{ $ofMonth->format('Y-m') }}</h1>
        <a class="p-2" href="{{ route('threads.month', ['month' => $ofMonth->addMonth()->format('Y-m')]) }}">
            &#10095;
        </a>
    </div>

    <form class="mb-3 dark:text-white" method="GET" :action="action" x-data="{
        uri: '{{ route('threads.month') }}',
        month: '{{ $ofMonth->format('Y-m') }}',
        get action() {
            return `${this.uri}/${this.month}`
        }
    }" x-ref="jump2">
        <input class="mr-2 cursor-pointer p-1" type="month" x-model="month"
               :style="darkMode && { 'color-scheme': 'dark' }">

        <button
                class="p-1 font-bold text-cyan-600 ring-1 ring-cyan-600 hover:bg-cyan-600 hover:text-white dark:text-cyan-500 dark:hover:bg-cyan-600 dark:hover:text-white">跳至月份</button>
    </form>

    <div class="bg-white shadow-md dark:bg-neutral-800">
        <div class="grid grid-cols-7 divide-x divide-teal-300">
            @foreach (['日', '一', '二', '三', '四', '五', '六'] as $day)
                <span class="bg-teal-500 py-1 text-center text-white md:py-3">{{ $day }}</span>
            @endforeach
        </div>

        @while ($date->isSameMonth($ofMonth) || $date->lte($ofMonth))
            <div
                 class="grid grid-cols-7 divide-x border border-t-0 dark:divide-gray-500 dark:border-gray-500">
                @for ($i = 0; $i < 7; $i++)
                    @php
                        $thread = $threads->firstWhere('date', $date->format('Y-m-d'));
                    @endphp

                    <a title="{{ $thread?->date }}" @if ($thread) href="" @endif
                       @class([
                           'aspect-square',
                           'p-1 md:p-2',
                           'relative',
                           'hover:bg-gray-100 dark:hover:bg-gray-600',
                           'after:absolute after:top-0 after:left-0 after:w-full after:h-full after:z-[1] after:outline after:outline after:md:outline-4 after:outline-sky-600 dark:after:outline-sky-300' => $date->isToday(),
                       ])>
                        <span @class([
                            'align-top',
                            'text-xs md:text-base',
                            'dark:text-white' => $date->isSameMonth($ofMonth),
                            'text-gray-300 dark:text-gray-500' => !$date->isSameMonth($ofMonth),
                        ])>
                            {{ $date->day }}
                        </span>

                        @if ($thread)
                            <span
                                  class="absolute inset-1/2 flex h-1/2 w-1/2 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full text-sm font-bold text-cyan-500 ring-2 ring-cyan-500 md:h-1/3 md:w-1/3 md:text-lg">
                                {{ $thread->posters_count }}
                            </span>
                        @elseif($date->diffInDays(today()) == 1 && $date->lt(today()))
                            <p
                               class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-xs text-gray-400">
                                預備中
                            </p>
                        @endif
                    </a>

                    @php
                        $date->addDay();
                    @endphp
                @endfor
            </div>
        @endwhile
    </div>
@endsection
