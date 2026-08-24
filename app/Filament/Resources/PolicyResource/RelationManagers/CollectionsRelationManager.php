<?php

namespace App\Filament\Resources\PolicyResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class CollectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'collections';

    protected static ?string $title = 'التحصيلات';

    protected static ?string $modelLabel = 'تحصيل';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->label(__('المبلغ المحصّل (ج.م)'))
                    ->numeric()
                    ->required(),
                Forms\Components\DatePicker::make('collected_at')
                    ->label(__('تاريخ التحصيل'))
                    ->required()
                    ->default(now()),
                Forms\Components\Textarea::make('notes')
                    ->label(__('ملاحظات'))
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('amount')
            ->columns([
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('المبلغ'))
                    ->numeric(decimalPlaces: 2)
                    ->suffix(__(' ج.م'))
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('collected_at')
                    ->label(__('تاريخ التحصيل'))
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('collector.name')
                    ->label(__('بواسطة'))
                    ->default('—'),
            ])
            ->defaultSort('collected_at', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('تسجيل تحصيل'))
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['collected_by'] = Auth::id();

                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('التحصيلات');
    }

    protected static function getModelLabel(): ?string
    {
        return __('تحصيل');
    }
}
