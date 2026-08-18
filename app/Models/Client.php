<?php

namespace App\Models;

use App\Enums\ClientType;
use App\Enums\PipelineStage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Client extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'type',
        'name',
        'phone',
        'email',
        'address',
        'national_id',
        'commercial_register',
        'pipeline_stage',
        'assigned_to',
        'documents',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'type' => ClientType::class,
            'pipeline_stage' => PipelineStage::class,
            'documents' => 'array',
        ];
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function policies(): HasMany
    {
        return $this->hasMany(Policy::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    public function followUps(): MorphMany
    {
        return $this->morphMany(FollowUp::class, 'followable')->latest();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logFillable();
    }
}
