<?php

namespace Mattoid\CheckinHistory\Listeners;

use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Mattoid\CheckinHistory\Model\UserCheckinHistory;
use Ziven\checkin\Event\checkinUpdated;

class DoCheckinHistory {
    protected $settings;
    protected $events;

    public function __construct(SettingsRepositoryInterface $settings, Dispatcher $events){
        $this->settings = $settings;
        $this->events = $events;
    }

    public function checkinHistory(checkinUpdated $event){
        $user = $event->user;

        $timezone = intval($this->settings->get('ziven-forum-checkin.checkinTimeZone', 0));
        $current_timestamp = time()+$timezone*60*60;

        $history = new UserCheckinHistory();
        $history->user_id = $user->id;
        $history->last_checkin_date = date('Y-m-d', $current_timestamp);
        $history->total_checkin_count = $user->total_checkin_count;
        $history->total_continuous_checkin_count = $user->total_continuous_checkin_count;
        $history->last_checkin_time = $user->last_checkin_time;
        $history->save();

    }
}
