<?php

/*
 * This file is part of mattoid/daily-check-in-history.
 *
 * Copyright (c) 2023 mattoid.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use Flarum\Extend;
use Flarum\Api\Serializer\BasicUserSerializer;
use Mattoid\CheckinHistory\Attributes\UserAttributes;
use Mattoid\CheckinHistory\Event\SupplementaryCheckinEvent;
use Mattoid\CheckinHistory\Listeners\SupplementaryCheckin;
use Mattoid\CheckinHistory\Middleware\UserAuthMiddleware;
use Mattoid\CheckinHistory\Listeners\DoCheckinHistory;
use Ziven\checkin\Event\checkinUpdated;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/less/forum.less')
        ->route('/u/{username}/checkin/history', 'mattoid-daily-check-in-history.forum.page.link-name'),
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/less/admin.less'),
    new Extend\Locales(__DIR__.'/locale'),

    (new Extend\Middleware("api"))->add(UserAuthMiddleware::class),

    (new Extend\Event())->listen(checkinUpdated::class, [DoCheckinHistory::class, 'checkinHistory']),
    (new Extend\Event())->listen(SupplementaryCheckinEvent::class, [SupplementaryCheckin::class, 'supplementCheckin']),

    (new Extend\ApiSerializer(BasicUserSerializer::class))
        ->attributes(UserAttributes::class),

    (new Extend\Routes('api'))
        ->get('/checkin/history', 'checkin.history', Mattoid\CheckinHistory\Api\Controller\ListCheckinHistoryController::class)
        ->post('/supplement/checkin', 'supplement.checkin', Mattoid\CheckinHistory\Api\Controller\PostCheckinHistoryController::class)
        ->post('/give/checkin/card', 'give.checkin-card', Mattoid\CheckinHistory\Api\Controller\PostGiveCheckinCardController::class)
];
