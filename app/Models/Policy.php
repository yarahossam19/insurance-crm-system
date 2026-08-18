<?php

namespace App\Models;

use App\Enums\PolicyStatus;
use App\Enums\PolicyType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Policy extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'client_id',
        'insurance_company_id',
        'policy_number',
        'type',
        'start_date',
        'end_date',
        'premium_amount',
        'commission_rate',
        'commission_amount',
        'employee_commission_amount',
        'net_office_commission',
        'status',
        'responsible_user_id',
        'documents',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'type' => PolicyType::class,
            'status' => PolicyStatus::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'premium_amount' => 'decimal:2',
            'commission_rate' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'employee_commission_amount' => 'decimal:2',
            'net_office_commission' => 'decimal:2',
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

    public function responsibleUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    public function collections(): HasMany
    {
        return $this->hasMany(Collection::class);
    }

    public function followUps(): MorphMany
    {
        return $this->morphMany(FollowUp::class, 'followable')->latest();
    }

    /**
     * Days remaining until the policy expires (negative if already expired).
     */
    public function getDaysToExpiryAttribute(): int
    {
        return (int) now()->startOfDay()->diffInDays($this->end_date->startOfDay(), false);
    }

    /**
     * Which renewal alert tier this policy currently falls into.
     */
    public function getRenewalTierAttribute(): ?string
    {
        if ($this->status === PolicyStatus::Cancelled) {
            return null;
        }

        $days = $this->days_to_expiry;

        return match (true) {
            $days < 0 => 'expired',
            $days <= 7 => '7',
            $days <= 15 => '15',
            $days <= 30 => '30',
            $days <= 60 => '60',
            $days <= 90 => '90',
            default => null,
        };
    }

    public function getCollectedTotalAttribute(): float
    {
        return (float) $this->collections()->sum('amount');
    }

    public function getOutstandingAttribute(): float
    {
        return max(0, (float) $this->net_office_commission - $this->collected_total);
    }

    /** Policies whose end_date falls within the given renewal window (days from today). */
    public function scopeExpiringWithin(Builder $query, int $daysFrom, int $daysTo): Builder
    {
        return $query
            ->where('status', '!=', PolicyStatus::Cancelled->value)
            ->whereBetween('end_date', [
                Carbon::now()->addDays($daysFrom)->startOfDay(),
                Carbon::now()->addDays($daysTo)->endOfDay(),
            ]);
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query
            ->where('status', '!=', PolicyStatus::Cancelled->value)
            ->where('end_date', '<', Carbon::now()->startOfDay());
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logFillable();
    }
}
