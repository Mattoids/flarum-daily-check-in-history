<?php

namespace Mattoid\CheckinHistory\Event;

use Flarum\Foundation\ValidationException;
use Flarum\Locale\Translator;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use Illuminate\Support\Arr;
use Mattoid\CheckinHistory\Model\UserCheckinHistory;

class CheckinEvent
{
    public $user;
    protected $settings;
    protected $translator;

    public function __construct(SettingsRepositoryInterface $settings, Translator $translator)
    {
        $this->settings = $settings;
        $this->translator = $translator;
    }

    public function supplementCheckin(User $user, String $checkinDate, int $totalContinuousCheckinCountHistory = 0): UserCheckinHistory {

        $rewardMoney = (double)$this->settings->get('mattoid-forum-checkin.reward-money') ?? 0;
        $consumption = (double)$this->settings->get('mattoid-forum-checkin.consumption') ?? 0;
        $spanDayCheckin = $this->settings->get('mattoid-forum-checkin.span-day-checkin');

        $userId = Arr::get($user, 'id');
        $totalCheckinCount = Arr::get($user, 'total_checkin_count');
        $totalContinuousCheckinCount = Arr::get($user, 'total_continuous_checkin_count');

        if ($user->checkin_card <= 0 && $user->money < ($consumption - $rewardMoney)) {
            throw new ValidationException(['message' => $this->translator->trans('mattoid-daily-check-in-history.api.error.span-day-checkin')]);
        }

        // 操作签到
        if ($user->checkin_card > 0) {
            // 有签到卡则扣除签到卡
            $user->checkin_card -= 1;
        } else {
            // 没有签到卡直接扣除金额
            $user->money -= $consumption;
        }
        $user->money += $rewardMoney;
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

        return $history;
    }
}
