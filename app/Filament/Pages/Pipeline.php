<?php

namespace App\Filament\Pages;

use App\Enums\PipelineStage;
use App\Models\Client;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class Pipeline extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-view-columns';

    protected static ?string $navigationGroup = 'العملاء والمبيعات';

    protected static ?string $navigationLabel = 'مسار المتابعة (Pipeline)';

    protected static ?string $title = 'مسار متابعة العملاء';

    protected static ?int $navigationSort = 11;

    protected static string $view = 'filament.pages.pipeline';

    /** @return Collection<string, Collection<int, Client>> */
    public function getColumnsProperty(): Collection
    {
        $clients = Client::with('assignedUser')
            ->orderByDesc('updated_at')
            ->get()
            ->groupBy(fn (Client $client) => $client->pipeline_stage->value);

        return collect(PipelineStage::cases())
            ->mapWithKeys(fn (PipelineStage $stage) => [
                $stage->value => $clients->get($stage->value, collect()),
            ]);
    }

    public function moveClient(int $clientId, string $direction): void
    {
        $client = Client::findOrFail($clientId);
        $stages = PipelineStage::cases();
        $currentIndex = array_search($client->pipeline_stage, $stages, true);

        $newIndex = $direction === 'next' ? $currentIndex + 1 : $currentIndex - 1;

        if ($newIndex < 0 || $newIndex >= count($stages)) {
            return;
        }

        $client->update(['pipeline_stage' => $stages[$newIndex]->value]);
    }

    public static function getNavigationGroup(): ?string
    {
        return __('العملاء والمبيعات');
    }

    public static function getNavigationLabel(): string
    {
        return __('مسار المتابعة (Pipeline)');
    }

    public function getTitle(): string
    {
        return __('مسار متابعة العملاء');
    }
}
