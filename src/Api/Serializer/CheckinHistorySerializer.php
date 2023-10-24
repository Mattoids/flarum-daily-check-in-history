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

use Flarum\Api\Serializer\AbstractSerializer;
use Flarum\Api\Serializer\PostSerializer;
use Flarum\Post\Post;
use Flarum\Settings\SettingsRepositoryInterface;
use Mattoid\CheckinHistory\Model\UserCheckinHistory;

class CheckinHistorySerializer extends AbstractSerializer
{
    protected $type = 'checkin.history';
    protected $settings;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultAttributes($history)
    {
        $checkinColor = $this->settings->get('mattoid-forum-checkin.checkin-color') ?? '#2756c6';
        $supplementaryColor = $this->settings->get('mattoid-forum-checkin.supplementary-color') ?? '#ff9900';
        $checkinColor = $checkinColor == '' ? '#2756c6' : $checkinColor;
        $supplementaryColor = $supplementaryColor == '' ? '#ff9900' : $supplementaryColor;
        $attributes = [
            'id'               => $history->id,
            'userId'           => $history->user_id,
            'type'             => $history->type,
            'totalCheckinCount'                 => $history->total_checkin_count,
            'totalContinuousCheckinCount'       => $history->total_continuous_checkin_count,
            'start'                  => $history->last_checkin_date,
            'time'                   => $history->last_checkin_time,
            'color'                  => $history->type == 1 ?  $supplementaryColor : $checkinColor
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
