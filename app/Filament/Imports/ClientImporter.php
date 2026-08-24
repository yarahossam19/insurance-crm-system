<?php

namespace App\Filament\Imports;

use App\Enums\ClientType;
use App\Models\Client;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ClientImporter extends Importer
{
    protected static ?string $model = Client::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label(__('الاسم'))
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('type')
                ->label(__('النوع (individual/company)'))
                ->rules(['nullable', 'in:individual,company']),
            ImportColumn::make('phone')
                ->label(__('التليفون'))
                ->rules(['nullable', 'max:50']),
            ImportColumn::make('email')
                ->label(__('البريد الإلكتروني'))
                ->rules(['nullable', 'email', 'max:255']),
            ImportColumn::make('national_id')
                ->label(__('الرقم القومي'))
                ->rules(['nullable', 'max:50']),
            ImportColumn::make('commercial_register')
                ->label(__('السجل التجاري'))
                ->rules(['nullable', 'max:50']),
            ImportColumn::make('address')
                ->label(__('العنوان'))
                ->rules(['nullable']),
            ImportColumn::make('notes')
                ->label(__('ملاحظات'))
                ->rules(['nullable']),
        ];
    }

    public function resolveRecord(): ?Client
    {
        // avoid duplicates: match an existing client by phone or email if either was provided
        if (filled($this->data['phone'] ?? null)) {
            $existing = Client::where('phone', $this->data['phone'])->first();
            if ($existing) {
                return $existing;
            }
        }

        if (filled($this->data['email'] ?? null)) {
            $existing = Client::where('email', $this->data['email'])->first();
            if ($existing) {
                return $existing;
            }
        }

        $client = new Client();
        $client->type = ClientType::Individual->value;

        return $client;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = __('تم استيراد ').number_format($import->successful_rows).__(' سجل بنجاح.');

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= __(' فشل استيراد ').number_format($failedRowsCount).__(' سجل.');
        }

        return $body;
    }
}
