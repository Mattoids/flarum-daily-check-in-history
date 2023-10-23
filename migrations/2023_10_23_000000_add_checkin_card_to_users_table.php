<?php

use Flarum\Database\Migration;

return Migration::addColumns('users', [
    'checkin_card' => ['integer', 'default' => 0, 'comment' => '签到卡数量']
]);
