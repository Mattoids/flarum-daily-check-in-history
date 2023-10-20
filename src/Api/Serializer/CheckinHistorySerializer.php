<?php

/*
 * This file is part of askvortsov/flarum-moderator-warnings
 *
 *  Copyright (c) 2021 Alexander Skvortsov.
 *
 *  For detailed copyright and license information, please view the
 *  LICENSE file that was distributed with this source code.
 */

namespace Mattoid\CheckinHistory\Api\Serializer;

use Mattoid\CheckinHistory\Model\UserCheckinHistory;
use Flarum\Api\Serializer\AbstractSerializer;
use Flarum\Api\Serializer\PostSerializer;
use Flarum\Post\Post;

class CheckinHistorySerializer extends AbstractSerializer
{
    protected $type = 'checkin.history';

    /**
     * {@inheritdoc}
     */
    protected function getDefaultAttributes($history)
    {
        $attributes = [
            'id'               => $history->id,
            'userId'           => $history->user_id,
            'name'             => $history->type ? '签到' : '补签',
            'totalCheckinCount'                 => $history->total_checkin_count,
            'totalContinuousCheckinCount'       => $history->total_continuous_checkin_count,
            'start'                   => $history->last_checkin_date,
            'time'                   => $history->last_checkin_time,
        ];

        return $attributes;
    }

    protected function format($text)
    {
        return UserCheckinHistory::getFormatter()->render($text, new Post());
    }

    protected function post($history)
    {
        return $this->hasOne($history, PostSerializer::class);
    }
}
