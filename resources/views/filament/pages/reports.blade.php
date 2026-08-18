<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($this->getQuickLinks() as $link)
            <a
                href="{{ $link['url'] }}"
                class="flex items-start gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
            >
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-500/10 dark:text-primary-400">
                    <x-filament::icon :icon="$link['icon']" class="h-5 w-5" />
                </span>

                <span>
                    <span class="block text-sm font-semibold text-gray-950 dark:text-white">
                        {{ $link['label'] }}
                    </span>
                    <span class="mt-0.5 block text-xs text-gray-500 dark:text-gray-400">
                        {{ $link['description'] }}
                    </span>
                </span>
            </a>
        @endforeach
    </div>
</x-filament-panels::page>
