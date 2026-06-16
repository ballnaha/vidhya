<x-layouts::app.sidebar :title="$title ?? null">
    <main class="min-h-screen px-4 pb-4 pt-3 lg:px-5 lg:pb-5 lg:pt-4">
        {{ $slot }}
    </main>
</x-layouts::app.sidebar>
