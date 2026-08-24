<?php

namespace App\Filament\Widgets;

use App\Models\Claim;
use App\Models\Client;
use App\Models\FollowUp;
use App\Models\Policy;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PendingFollowUpsTable extends BaseWidget
{
    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 6;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                FollowUp::query()
                    ->whereNotNull('next_follow_up_at')
                    ->where('next_follow_up_at', '<=', now()->endOfDay()->addDays(7))
                    ->with(['followable', 'user'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('followable_type')
                    ->label(__('النوع'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        Client::class => __('عميل'),
                        Policy::class => __('وثيقة'),
                        Claim::class => __('مطالبة'),
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('followable')
                    ->label(__('العنصر'))
                    ->getStateUsing(fn (FollowUp $record): ?Model => $record->followable)
                    ->formatStateUsing(fn (?Model $state): string => match (true) {
                        $state instanceof Client => $state->name,
                        $state instanceof Policy => $state->policy_number,
                        $state instanceof Claim => $state->claim_number,
                        default => '—',
                    })
                    ->url(fn (FollowUp $record): ?string => match (true) {
                        $record->followable instanceof Client => route('filament.admin.resources.clients.view', $record->followable),
                        $record->followable instanceof Policy => route('filament.admin.resources.policies.view', $record->followable),
                        $record->followable instanceof Claim => route('filament.admin.resources.claims.view', $record->followable),
                        default => null,
                    })
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('note')
                    ->label(__('الملاحظة'))
                    ->limit(50)
                    ->wrap(),
                Tables\Columns\TextColumn::make('next_follow_up_at')
                    ->label(__('موعد المتابعة القادمة'))
                    ->dateTime('Y-m-d H:i')
                    ->color(fn ($state) => $state && $state->isPast() ? 'danger' : 'warning')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('بواسطة')),
            ])
            ->defaultSort('next_follow_up_at')
            ->paginated([5, 10, 25])
            ->emptyStateHeading(__('لا توجد متابعات مستحقة خلال الأسبوع القادم'))
            ->emptyStateIcon('heroicon-o-check-circle');
    }

    protected function getTableHeading(): string
    {
        return __('المهام والمتابعات المستحقة');
    }
}
