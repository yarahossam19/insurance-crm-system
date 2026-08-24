<?php

namespace App\Filament\Resources\ClientResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class FollowUpsRelationManager extends RelationManager
{
    protected static string $relationship = 'followUps';

    protected static ?string $title = 'سجل المتابعات';

    protected static ?string $modelLabel = 'متابعة';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('note')
                    ->label(__('ملاحظة المتابعة'))
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('result')
                    ->label(__('نتيجة الاتصال')),
                Forms\Components\DateTimePicker::make('next_follow_up_at')
                    ->label(__('موعد المتابعة القادمة')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('note')
            ->columns([
                Tables\Columns\TextColumn::make('note')
                    ->label(__('الملاحظة'))
                    ->wrap()
                    ->limit(80),
                Tables\Columns\TextColumn::make('result')
                    ->label(__('النتيجة'))
                    ->badge()
                    ->default('—'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('بواسطة')),
                Tables\Columns\TextColumn::make('next_follow_up_at')
                    ->label(__('المتابعة القادمة'))
                    ->dateTime('Y-m-d H:i')
                    ->color(fn ($state) => $state && $state->isPast() ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('تاريخ الإضافة'))
                    ->dateTime('Y-m-d H:i'),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('إضافة متابعة'))
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = Auth::id();

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
        return __('سجل المتابعات');
    }

    protected static function getModelLabel(): ?string
    {
        return __('متابعة');
    }
}
