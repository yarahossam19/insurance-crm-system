<?php

namespace App\Console\Commands;

use App\Enums\PolicyStatus;
use App\Models\Policy;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class NotifyRenewals extends Command
{
    protected $signature = 'renewals:notify';

    protected $description = 'أرسل إشعارات للموظف المسؤول وللإدارة عن الوثائق اللي دخلت مرحلة تجديد جديدة';

    public function handle(): int
    {
        /** @var Collection<int, User> $managers */
        $managers = User::role(['super_admin', 'Manager'])->get();

        $policies = Policy::query()
            ->where('status', '!=', PolicyStatus::Cancelled->value)
            ->with(['client', 'responsibleUser'])
            ->get();

        $notified = 0;

        foreach ($policies as $policy) {
            $tier = $policy->renewal_tier;

            if ($tier === null || $tier === $policy->last_notified_tier) {
                continue;
            }

            $recipients = $managers->when(
                $policy->responsibleUser,
                fn (Collection $users) => $users->push($policy->responsibleUser)
            )->unique('id');

            $isExpired = $tier === 'expired';

            $notification = Notification::make()
                ->title($isExpired
                    ? __('وثيقة منتهية: :number', ['number' => $policy->policy_number])
                    : __('تجديد خلال :tier يوم: :number', ['tier' => $tier, 'number' => $policy->policy_number]))
                ->body(__('العميل: :client — تنتهي في :date', [
                    'client' => $policy->client?->name,
                    'date' => $policy->end_date->format('Y-m-d'),
                ]))
                ->icon($isExpired ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-arrow-path')
                ->color($isExpired || in_array($tier, ['7', '15'], true) ? 'danger' : 'warning')
                ->actions([
                    Action::make('view')
                        ->label(__('عرض الوثيقة'))
                        ->url(route('filament.admin.resources.policies.view', $policy))
                        ->markAsRead(),
                ]);

            foreach ($recipients as $recipient) {
                $notification->sendToDatabase($recipient);
            }

            $policy->update(['last_notified_tier' => $tier]);
            $notified++;
        }

        $this->info(__('تم إرسال :count إشعار تجديد.', ['count' => $notified]));

        return self::SUCCESS;
    }
}
