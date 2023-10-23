<?php

namespace Mattoid\CheckinHistory\Event;

use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\Exception\PermissionDeniedException;
use Flarum\User\User;
use Illuminate\Support\Arr;
use Mattoid\CheckinHistory\Model\UserCheckinHistory;

class CheckinEvent
{
    public $user;
    protected $settings;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    public function supplementCheckin(User $user, String $checkinDate, int $totalContinuousCheckinCountHistory = 0): UserCheckinHistory {

        $rewardMoney = $this->settings->get('mattoid-forum-checkin.reward-money');
        $consumption = $this->settings->get('mattoid-forum-checkin.consumption');
        $spanDayCheckin = $this->settings->get('mattoid-forum-checkin.span-day-checkin');

        $userId = Arr::get($user, 'id');
        $totalCheckinCount = Arr::get($user, 'total_checkin_count');
        $totalContinuousCheckinCount = Arr::get($user, 'total_continuous_checkin_count');

        // 操作签到
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
        $history->total_continuous_checkin_count = $user->total_continuous_checkin_count;
        $history->save();

        return $history;
    }
}
