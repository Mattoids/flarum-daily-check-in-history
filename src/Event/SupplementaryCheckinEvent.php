<?php

namespace Mattoid\CheckinHistory\Event;

use Flarum\User\User;

class SupplementaryCheckinEvent
{
    public $user;
    public $checkinDate;
    public $checkinCount = 0;
    public $totalContinuousCheckinCountHistory = 0;

    public function __construct(User $user = null, String $checkinDate, int $totalContinuousCheckinCountHistory = 0, int $checkinCount = 0)
    {
        $this->user = $user;
        $this->checkinDate = $checkinDate;
        $this->checkinCount = $checkinCount;
        $this->totalContinuousCheckinCountHistory = $totalContinuousCheckinCountHistory;
    }
}
