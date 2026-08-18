<x-filament-panels::page>
    <div class="flex gap-4 overflow-x-auto pb-4">
        @foreach ($this->columns as $stageValue => $clients)
            @php($stage = \App\Enums\PipelineStage::from($stageValue))
            <div class="w-72 shrink-0">
                <x-filament::section :heading="$stage->getLabel()" compact>
                    <x-slot name="heading">
                        <div class="flex items-center gap-2">
                            <x-filament::icon :icon="$stage->getIcon()" class="h-4 w-4" />
                            <span>{{ $stage->getLabel() }}</span>
                        </div>
                    </x-slot>

                    <x-slot name="headerEnd">
                        <x-filament::badge :color="$stage->getColor()">
                            {{ $clients->count() }}
                        </x-filament::badge>
                    </x-slot>

                    <div class="flex flex-col gap-2">
                        @forelse ($clients as $client)
                            <div class="rounded-lg border border-gray-200 bg-white p-3 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                <a
                                    href="{{ \App\Filament\Resources\ClientResource::getUrl('view', ['record' => $client]) }}"
                                    class="text-sm font-semibold text-gray-950 hover:underline dark:text-white"
                                >
                                    {{ $client->name }}
                                </a>

                                <div class="mt-1 flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $client->phone ?? '—' }}
                                    </span>

                                    @if ($client->assignedUser)
                                        <x-filament::badge color="gray" size="xs">
                                            {{ $client->assignedUser->name }}
                                        </x-filament::badge>
                                    @endif
                                </div>

                                <div class="mt-2 flex items-center justify-between">
                                    <x-filament::icon-button
                                        icon="heroicon-o-arrow-right"
                                        label="مرحلة سابقة"
                                        size="sm"
                                        wire:click="moveClient({{ $client->id }}, 'prev')"
                                    />

                                    <x-filament::icon-button
                                        icon="heroicon-o-arrow-left"
                                        label="مرحلة تالية"
                                        size="sm"
                                        color="primary"
                                        wire:click="moveClient({{ $client->id }}, 'next')"
                                    />
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-xs text-gray-400 py-6">لا يوجد عملاء في هذه المرحلة</p>
                        @endforelse
                    </div>
                </x-filament::section>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
