<!DOCTYPE html>
<html class="scroll-smooth" lang="zh-Hant" x-data="{
    darkMode: $persist(false),
    showAllVideo: $persist(false)
}" x-init="$watch('darkMode', () => $el.classList.toggle('dark'))">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>mid9songposts</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/js-beautify/1.14.7/beautify-html.min.js"></script>

    {{-- Alpine Plugins Here --}}
    <script defer src="https://unpkg.com/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>

    {{-- Alpine Core Here --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        if (localStorage.getItem('_x_darkMode') == 'true') {
            document.querySelector('html').classList.add('dark');
        }

        tailwind.config = {
            darkMode: 'class'
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="min-h-screen bg-neutral-100 dark:bg-gray-900" x-data x-ref="body">
    <header class="sticky top-0 z-50 w-full bg-cyan-600 text-white">
        <div class="container mx-auto flex justify-between py-2 px-2 md:py-3">
            <div>
                <a class="text-base font-bold md:text-lg" href="/">半夜歌串一人一首</a>
                <a class="text-sm md:text-base" href="{{ route('links.index') }}">歌曲列表</a>
            </div>

            <button x-text="darkMode ? '🌒' : '☀️'" @click="darkMode = !darkMode"></button>
        </div>
    </header>

    <div class="mx-auto my-2 max-w-4xl px-2 dark:text-white md:my-6">
        @yield('content')
    </div>

    <div class="bg-cyan-600 p-1 text-center text-xs text-white md:text-sm">
        <small>The end of the page.</small>
    </div>
</body>

</html>
