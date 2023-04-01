@extends('layout')

@php
    $title = '歌曲列表';

    if (request()->filled(['account', 'search'])) {
        $title = '搜尋 ' . request('account') . ' 張貼的 ' . request('search');
    } elseif (request('account')) {
        $title = '搜尋 帳號: ' . request('account');
    } elseif (request('search')) {
        $title = '搜尋 標題: ' . request('search');
    }
@endphp

@section('title', $title)

@section('content')
    {{ $links->onEachSide(1)->withQueryString()->links() }}

    <div class="mb-3">
        <div class="mb-3 flex flex-wrap justify-center gap-2 text-sm md:justify-between">
            <form class="flex gap-2" action="{{ route('links.index') }}" method="GET">
                <div class="inline-block bg-white shadow dark:bg-neutral-700" x-data="{ account: '{{ request('account') }}' }">
                    <input class="w-20 bg-transparent p-1 md:w-24" name="account" type="text" placeholder="帳號"
                        :style="darkMode && { 'color-scheme': 'dark' }" x-model="account" x-ref="account">
                    <span class="inline-block h-full cursor-pointer px-1"
                        :class="account ? 'text-black dark:text-gray-300' : 'text-gray-300 dark:text-gray-500'"
                        @click="account = ''; $refs.account.focus()">&times;</span>
                </div>
                <div class="inline-block bg-white shadow dark:bg-neutral-700"
                    x-data='{ search: {{ json_encode(request('search')) }} }'>
                    <input class="w-20 bg-transparent p-1 md:w-24" name="search" type="text" placeholder="標題"
                        :style="darkMode && { 'color-scheme': 'dark' }" x-model="search" x-ref="search">
                    <span class="inline-block h-full cursor-pointer px-1"
                        :class="search ? 'text-black dark:text-gray-300' : 'text-gray-300 dark:text-gray-500'"
                        @click="search = ''; $refs.search.focus()">&times;</span>
                </div>
                <button
                    class="p-1 text-cyan-600 ring-1 ring-cyan-600 hover:bg-cyan-600 hover:text-white dark:text-cyan-500 dark:hover:bg-cyan-600 dark:hover:text-white">
                    搜尋
                </button>
                @if (request('account') || request('search'))
                    <a class="p-1 text-cyan-600 ring-1 ring-cyan-600 hover:bg-cyan-600 hover:text-white dark:text-cyan-500 dark:hover:bg-cyan-600 dark:hover:text-white"
                        href="{{ route('links.index') }}">
                        清除
                    </a>
                @endif
            </form>
            <div class="flex gap-2">
                <a class="p-1 text-cyan-600 ring-1 ring-cyan-600 hover:bg-cyan-600 hover:text-white dark:text-cyan-500 dark:hover:bg-cyan-600 dark:hover:text-white"
                    href="{{ request()->fullUrlWithQuery([
                        'sort' => request('sort', 'desc') === 'desc' ? 'asc' : 'desc',
                        'page' => 1,
                    ]) }}">
                    時間{{ request('sort', 'desc') === 'desc' ? '↓' : '↑' }}
                </a>
                <button
                    class="p-1 text-cyan-600 ring-1 ring-cyan-600 hover:bg-cyan-600 hover:text-white dark:text-cyan-500 dark:hover:bg-cyan-600 dark:hover:text-white"
                    @click="showAllVideo = !showAllVideo">
                    <span x-text="showAllVideo ? '關閉' : '開啟'"></span>影片
                </button>
            </div>
        </div>

        @forelse ($links as $link)
            <div class="mb-2 flex flex-wrap" x-data="{ showVideo: false }">
                <div class="grow self-start bg-white shadow-md dark:bg-neutral-700 md:basis-1/2">
                    <div class="border-b p-2 dark:border-neutral-600">
                        <span class="cursor-pointer text-cyan-600 dark:text-cyan-400" title="點擊顯示影片"
                            @click="showVideo = !showVideo">
                            {{ $link->title }}
                        </span>
                        <a class="px-2 text-gray-500 dark:text-gray-300" href="{{ $link->general() }}" title="開啟外部網頁"
                            target="_blank">&rdsh;</a>
                    </div>

                    <div class="flex items-center gap-2 p-1 pl-2 text-sm text-gray-500 dark:text-gray-300">
                        <a href="{{ route('links.index', ['account' => $link->poster->account]) }}">
                            <img class="w-8 rounded-full" src="{{ $link->poster->avatar }}"
                                alt="{{ $link->poster->account }}">
                        </a>
                        <div>
                            <a class="hover:text-cyan-500 dark:hover:text-cyan-400"
                                href="{{ route('links.index', ['account' => $link->poster->account]) }}"
                                title="搜尋 {{ $link->poster->account }}">
                                {{ $link->poster->account }}
                                ({{ $link->poster->name }})
                            </a>
                            <span>發布於</span>
                            <a class="hover:text-cyan-500 dark:hover:text-cyan-400"
                                href="{{ route('threads.show', ['thread' => $link->thread->date]) . '#' . $link->post->no }}"
                                title="{{ $link->thread->title }}">
                                {{ $link->post->created_at }}
                            </a>
                        </div>
                    </div>
                </div>

                <template x-if="showVideo || showAllVideo">
                    <iframe class="aspect-video grow basis-full md:basis-1/2" src="{{ $link->embedded() }}" frameborder="0"
                        loading="lazy" allowfullscreen></iframe>
                </template>
            </div>
        @empty
            <h1 class="text-center text-xl text-gray-500 dark:text-gray-400">查無結果</h1>
        @endforelse
    </div>

    {{ $links->onEachSide(1)->withQueryString()->links() }}
@endsection
