@extends('layout')

@section('content')
    <h1
        class="flex flex-wrap items-center justify-between border-b bg-white p-2 text-xl text-cyan-600 shadow-md dark:border-neutral-600 dark:bg-neutral-700 dark:text-cyan-400">
        {{ $thread->title }}
        <small class="text-sm text-gray-400">{{ $thread->date }}</small>
    </h1>

    @foreach ($thread->posts as $post)
        <div
            class="{{ $post->links->isEmpty() ? 'opacity-50' : '' }} border-b bg-white p-2 shadow-md dark:border-neutral-600 dark:bg-neutral-700">
            <h6 class="flex items-center gap-3 py-2">
                <img src="{{ $post->poster->avatar }}" alt="{{ $post->poster->account }}">

                {{ $post->poster->account }}

                <span class="hidden text-gray-400 sm:inline">發布於 {{ $post->created_at }}</span>

                <button
                    class="ml-auto p-1 text-sm text-cyan-600 ring-1 ring-cyan-600 hover:bg-cyan-600 hover:text-white dark:text-cyan-500 dark:hover:bg-cyan-600 dark:hover:text-white"
                    x-data
                    @click="$dispatch('see-post', {
                        avatar: '{{ $post->poster->avatar }}',
                        account: '{{ $post->poster->account }}',
                        created_at: '{{ $post->created_at }}',
                        content: `{{ app(HTMLPurifier::class)->purify($post->content) }}`,
                        raw: `{{ $post->content }}`
                    });">
                    內文
                </button>
            </h6>

            <div class="flex flex-col gap-2">
                @foreach ($post->links as $link)
                    <iframe class="mx-auto aspect-video w-full max-w-2xl" src="{{ $link->embedded() }}" frameborder="0"
                        loading="lazy" allowfullscreen></iframe>
                @endforeach
            </div>

            @if ($post->comments->isNotEmpty())
                <div class="flex gap-2 pt-3" x-data="{ isCollapse: true }">
                    <small class="mt-2 whitespace-nowrap text-center">
                        <div>留言</div>

                        @if ($post->comments->count() > 3)
                            <button
                                class="mt-2 p-1 text-sm text-cyan-600 ring-1 ring-cyan-600 hover:bg-cyan-600 hover:text-white dark:text-cyan-400 dark:hover:bg-cyan-500 dark:hover:text-white"
                                x-text="isCollapse ? '3+' : '—'" @click="isCollapse = !isCollapse"></button>
                        @endif
                    </small>
                    <div class="flex flex-col gap-2">
                        @foreach ($post->comments as $index => $comment)
                            <div class="flex items-center gap-2" x-cloak
                                x-show="{{ $index >= 3 ? '!isCollapse' : 'true' }}">
                                <img class="self-start rounded-full" src="{{ $comment->poster->avatar }}">

                                <small class="break-all">
                                    {{ $comment->poster->account }}:
                                    {{ $comment->content }}
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
            {avatar, account, created_at, content, raw} = $event.detail;
            isShow = true;
            $refs.body.classList.add('overflow-hidden');
        ">
        <div
            class="absolute top-1/2 left-1/2 flex max-h-[80vh] w-full max-w-3xl -translate-x-1/2 -translate-y-1/2 flex-col bg-white dark:bg-neutral-700">
            <header class="flex items-center justify-between border-b p-3 dark:border-neutral-600">
                <div class="flex items-center gap-2">
                    <img :src="avatar">

                    <span x-text="account"></span>

                    <span class="text-gray-400" x-text="`發布於 ${created_at}`"></span>
                </div>

                <button @click="close()">🞩</button>
            </header>

            <div class="relative min-h-[50px] overflow-y-auto p-2">
                <div class="absolute top-0 right-2 flex text-sm">
                    <button class="border border-gray-400 p-0.5" :class="mode === 'WYSIWYG' && 'bg-gray-400 text-white'"
                        @click="mode = 'WYSIWYG'">所見所得</button>
                    <button class="border border-gray-400 p-0.5" :class="mode === 'html' && 'bg-gray-400 text-white'"
                        @click="mode = 'html'">&#60;html&#62;</button>
                </div>

                <div x-html="content" x-show="mode === 'WYSIWYG'"></div>

                <pre x-show="mode === 'html'"
                    x-text="html_beautify(raw, {'indent-size': 2, 'wrap-line-length': 120, 'wrap-attributes': 'force'})"></pre>
            </div>
        </div>
    </div>
@endsection
