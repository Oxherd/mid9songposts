<!DOCTYPE html>
<html lang="zh-Hant" x-data="{ darkMode: $persist(false) }" x-init="$watch('darkMode', () => $el.classList.toggle('dark'))">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>mid9songposts</title>

    <script src="https://cdn.tailwindcss.com"></script>

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
</head>

<body class="min-h-screen bg-neutral-100 dark:bg-gray-900">
    <header class="bg-cyan-600 text-white">
        <div class="container relative mx-auto py-3">
            <a class="pl-2 md:pl-0" href="/"><b>半夜歌串一人一首</b></a>

            <button class="absolute top-3 right-3" x-text="darkMode ? '🌒' : '☀️'"
                    @click="darkMode = !darkMode"></button>
        </div>
    </header>

    <div class="mx-auto my-3 max-w-4xl px-2 md:my-6">
        @yield('content')
    </div>
</body>

</html>
