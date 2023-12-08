<?php

namespace Mattoid\CheckinHistory\Listeners;

use Flarum\Foundation\ValidationException;
use Flarum\Locale\Translator;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Events\Dispatcher;
use Mattoid\CheckinHistory\Event\SupplementaryCheckinEvent;
use Mattoid\CheckinHistory\Model\UserCheckinHistory;

class SupplementaryCheckin
{
    public $user;
    protected $settings;
    protected $translator;
    protected $events;

    public function __construct(SettingsRepositoryInterface $settings, Translator $translator, Dispatcher $events)
    {
        $this->settings = $settings;
        $this->translator = $translator;
        $this->events = $events;
    }

    public function supplementCheckin(SupplementaryCheckinEvent $event): UserCheckinHistory {

        $user = $event->user;
        $checkinDate = $event->checkinDate;
        $checkinCount = $event->checkinCount;
        $totalContinuousCheckinCountHistory = $event->totalContinuousCheckinCountHistory;

        $rewardMoney = (double)$this->settings->get('mattoid-forum-checkin.reward-money') ?? 0;
        $consumption = (double)$this->settings->get('mattoid-forum-checkin.consumption') ?? 0;
        $checkinCard = (double)$this->settings->get('mattoid-forum-checkin.checkin-card') ?? 0;
        $checkinIncrease = (double)$this->settings->get('mattoid-forum-checkin.checkin-increase') ?? 0;
        $spanDayCheckin = $this->settings->get('mattoid-forum-checkin.span-day-checkin');

        $userId = Arr::get($user, 'id');
        $totalCheckinCount = Arr::get($user, 'total_checkin_count');
        $totalContinuousCheckinCount = Arr::get($user, 'total_continuous_checkin_count');

        $consumptionMoney = $consumption * ($checkinIncrease * $checkinCount / 100 + 1);

        if ($checkinCard > 0 && $user->checkin_card <= 0) {
            throw new ValidationException(['message' => $this->translator->trans('mattoid-daily-check-in-history.api.error.insufficient-checkin-card')]);
        }
        if ($user->checkin_card <= 0 && ($user->money < ($consumption - $rewardMoney) || $user->money < ($consumptionMoney - $rewardMoney))) {
            throw new ValidationException(['message' => $this->translator->trans('mattoid-daily-check-in-history.api.error.insufficient-balance')]);
        }


        // 操作签到
        $user->money += $rewardMoney;
        if ($user->checkin_card > 0) {
            // 有签到卡则扣除签到卡
            $user->checkin_card -= 1;
        } else {
            // 没有签到卡直接扣除金额
            $user->money -= $consumptionMoney;
        }
        $user->total_checkin_count=$totalCheckinCount + 1;
        $user->total_continuous_checkin_count=$totalContinuousCheckinCount + $totalContinuousCheckinCountHistory + 1;
        $user->save();

        // 记录补签数据
        $history = new UserCheckinHistory();
        $history->type = 1;
        $history->user_id = $userId;
        $history->last_checkin_date = $checkinDate;
        $history->last_checkin_time = date('Y-m-d h:i:s');
        $history->total_checkin_count = $user->total_checkin_count;
        $history->total_continuous_checkin_count = $totalContinuousCheckinCountHistory + 1;
        $history->save();

        if (class_exists('AntoineFr\Money\Event\MoneyUpdated')) {
            $this->events->dispatch(new \AntoineFr\Money\Event\MoneyUpdated($user));
        }

        return $history;
    }
}
