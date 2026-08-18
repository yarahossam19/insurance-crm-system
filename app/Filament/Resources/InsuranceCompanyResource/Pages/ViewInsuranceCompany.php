<?php

namespace App\Filament\Resources\InsuranceCompanyResource\Pages;

use App\Filament\Resources\InsuranceCompanyResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInsuranceCompany extends ViewRecord
{
    protected static string $resource = InsuranceCompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
