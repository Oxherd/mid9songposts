@extends('layout')

@section('content')
    <div
        class="flex flex-wrap items-center gap-2 border-b bg-white p-2 shadow-md dark:border-neutral-600 dark:bg-neutral-700 md:justify-between">
        <h1 class="text-xl text-gray-600 dark:text-gray-200">
            {{ $thread->title }}
        </h1>
        <div class="flex grow items-center justify-between gap-2 text-sm md:justify-end">
            <small class="text-gray-400">{{ $thread->date }}</small>

            <button
                class="p-1 text-cyan-600 ring-1 ring-cyan-600 hover:bg-cyan-600 hover:text-white dark:text-cyan-500 dark:hover:bg-cyan-600 dark:hover:text-white"
                @click="showAllVideo = !showAllVideo">
                <span x-text="showAllVideo ? '關閉' : '開啟'"></span>影片
            </button>
        </div>
    </div>

    @foreach ($thread->posts as $post)
        <a class="invisible relative -top-20 block" id="{{ $post->poster->account }}"></a>
        <div
            class="{{ $post->links->isEmpty() ? 'opacity-50' : '' }} border-b bg-white p-2 shadow-md dark:border-neutral-600 dark:bg-neutral-700">
            <h6 class="flex items-center gap-3 py-2">
                <a href="{{ route('links.index', ['account' => $post->poster->account]) }}">
                    <img src="{{ $post->poster->avatar }}" alt="{{ $post->poster->account }}">
                </a>

                <div class="flex flex-col items-start md:flex-row md:items-center md:gap-2">
                    <a class="hover:text-cyan-500 dark:hover:text-cyan-400"
                        href="{{ route('links.index', ['account' => $post->poster->account]) }}"
                        title="搜尋 {{ $post->poster->account }}">
                        {{ $post->poster->account }} ({{ $post->poster->name }})
                    </a>

                    <span class="text-sm text-gray-400">發布於 {{ $post->created_at }}</span>
                </div>

                <button
                    class="ml-auto p-1 text-sm text-cyan-600 ring-1 ring-cyan-600 hover:bg-cyan-600 hover:text-white dark:text-cyan-500 dark:hover:bg-cyan-600 dark:hover:text-white"
                    x-data
                    @click="$dispatch('see-post', {
                        avatar: '{{ $post->poster->avatar }}',
                        account: '{{ $post->poster->account }}',
                        name: '{{ $post->poster->name }}',
                        created_at: '{{ $post->created_at }}',
                        content: `{{ app(HTMLPurifier::class)->purify($post->content) }}`,
                        raw: `{{ $post->content }}`
                    });">
                    內文
                </button>
            </h6>

            <div class="my-5 flex flex-col gap-2">
                @foreach ($post->links as $link)
                    <div x-data="{ showVideo: false }">
                        <div x-show="!showVideo && !showAllVideo">
                            <span class="cursor-pointer text-cyan-600 dark:text-cyan-400" title="點擊開啟影片"
                                @click="showVideo = true">
                                {{ $link->title }}
                            </span>
                            <a class="px-2" href="{{ $link->general() }}" title="開啟外部網頁" target="_blank">&rdsh;</a>
                        </div>

                        <iframe class="mx-auto aspect-video w-full max-w-2xl" src="{{ $link->embedded() }}" x-cloak
                            x-show="showVideo || showAllVideo" frameborder="0" loading="lazy" allowfullscreen></iframe>
                    </div>
                @endforeach
            </div>

            @if ($post->comments->isNotEmpty())
                <div class="pt-3" x-data="{ isCollapse: true }">
                    <div class="mb-2 ml-2 flex items-center gap-2 text-xs">
                        <span>留言</span>

                        @if ($post->comments->count() > 3)
                            <button
                                class="p-1 text-cyan-600 ring-1 ring-cyan-600 hover:bg-cyan-600 hover:text-white dark:text-cyan-400 dark:hover:bg-cyan-500 dark:hover:text-white"
                                x-text="isCollapse ? '3+' : '—'" @click="isCollapse = !isCollapse"></button>
                        @endif
                    </div>
                    <div class="flex flex-col gap-2">
                        @foreach ($post->comments as $index => $comment)
                            <div class="flex items-center gap-2" x-cloak
                                x-show="{{ $index >= 3 ? '!isCollapse' : 'true' }}">
                                <a class="self-start"
                                    href="{{ route('links.index', ['account' => $comment->poster->account]) }}">
                                    <img class="max-w-none rounded-full" src="{{ $comment->poster->avatar }}">
                                </a>

                                <small class="break-all">
                                    <a class="hover:text-cyan-500 dark:hover:text-cyan-400"
                                        href="{{ route('links.index', ['account' => $comment->poster->account]) }}"
                                        title="搜尋 {{ $comment->poster->account }}">
                                        {{ $comment->poster->account }} ({{ $comment->poster->name }}):
                                    </a>
                                    {{ app(HTMLPurifier::class)->purify($comment->content) }}
                                </small>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endforeach

    <div class="fixed top-0 left-0 h-screen w-screen bg-black bg-opacity-40" x-data="{
        avatar: '',
        account: '',
        name: '',
        created_at: '',
        content: '',
        raw: '',
        mode: 'WYSIWYG',
        isShow: false,
        close() {
            this.isShow = false;
            $refs.body.classList.remove('overflow-hidden');
        }
    }"
        x-show="Boolean(isShow)" x-cloak @click="if ($event.target == $el) close()"
        @see-post.window="
            {avatar, account, name, created_at, content, raw} = $event.detail;
            isShow = true;
            $refs.body.classList.add('overflow-hidden');
        ">
        <div
            class="absolute top-1/3 left-1/2 flex max-h-[60vh] w-full max-w-3xl -translate-x-1/2 -translate-y-1/3 flex-col bg-white dark:bg-neutral-700">
            <header class="flex items-center justify-between border-b p-3 dark:border-neutral-600">
                <div class="flex items-center gap-2">
                    <img :src="avatar">

                    <div class="flex flex-wrap md:gap-2">
                        <div>
                            <span x-text="account"></span>
                            (<span x-text="name"></span>)
                        </div>
                        <span class="text-gray-400" x-text="`發布於 ${created_at}`"></span>
                    </div>
                </div>

                <button @click="close()">&#10005;</button>
            </header>

            <div class="relative min-h-[50px] overflow-y-auto p-2">
                <div class="absolute top-0 right-2 flex text-sm">
                    <button class="border border-gray-400 p-0.5" :class="mode === 'WYSIWYG' && 'bg-gray-400 text-white'"
                        @click="mode = 'WYSIWYG'">所見所得</button>
                    <button class="border border-gray-400 p-0.5" :class="mode === 'html' && 'bg-gray-400 text-white'"
                        @click="mode = 'html'">&#60;html&#62;</button>
                </div>

                <div class="my-2" x-html="content" x-show="mode === 'WYSIWYG'"></div>

                <pre class="my-2" x-show="mode === 'html'"
                    x-text="html_beautify(raw, {'indent-size': 2, 'wrap-line-length': 120, 'wrap-attributes': 'force'})"></pre>
            </div>
        </div>
    </div>
@endsection
