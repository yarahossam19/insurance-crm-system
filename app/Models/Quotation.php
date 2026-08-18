<?php

namespace App\Models;

use App\Enums\PolicyType;
use App\Enums\QuotationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Quotation extends Model
{
    use LogsActivity;

    protected $fillable = [
        'client_id',
        'insurance_company_id',
        'coverage_type',
        'amount',
        'status',
        'documents',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'coverage_type' => PolicyType::class,
            'status' => QuotationStatus::class,
            'amount' => 'decimal:2',
            'documents' => 'array',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function insuranceCompany(): BelongsTo
    {
        return $this->belongsTo(InsuranceCompany::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logFillable();
    }
}
