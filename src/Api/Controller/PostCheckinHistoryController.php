<?php

/*
 * This file is part of askvortsov/flarum-moderator-warnings
 *
 *  Copyright (c) 2021 Alexander Skvortsov.
 *
 *  For detailed copyright and license information, please view the
 *  LICENSE file that was distributed with this source code.
 */

namespace Mattoid\CheckinHistory\Api\Controller;

use DateTime;
use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Foundation\ValidationException;
use Flarum\Http\RequestUtil;
use Flarum\User\Exception\PermissionDeniedException;
use Illuminate\Support\Arr;
use Mattoid\CheckinHistory\Api\Serializer\CheckinHistorySerializer;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Mattoid\CheckinHistory\Event\CheckinEvent;
use Mattoid\CheckinHistory\Model\UserCheckinHistory;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Flarum\Locale\Translator;

class PostCheckinHistoryController extends AbstractCreateController
{
    public $serializer = CheckinHistorySerializer::class;

    public $include = ['post', 'post.discussion', 'post.user', 'user'];

    protected $translator;
    protected $settings;
    protected $events;
    protected $event;

    public function __construct(SettingsRepositoryInterface $settings, Dispatcher $events, Translator $translator, CheckinEvent $event)
    {
        $this->translator = $translator;
        $this->settings = $settings;
        $this->events = $events;
        $this->event = $event;
    }

    /**
     * Get the data to be serialized and assigned to the response document.
     *
     * @param ServerRequestInterface $request
     * @param Document               $document
     *
     * @throws PermissionDeniedException
     *
     * @return mixed
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {

        $actor = RequestUtil::getActor($request);
        $userId = Arr::get($actor, 'id');
        $checkinDate = Arr::get($request->getParsedBody(), 'date');

        if (!$actor->can('checkin.allowSupplementaryCheckIn')) {
            throw new PermissionDeniedException();
        }

        if (!empty($checkinDate)) {
            $startDate = new DateTime($checkinDate);
            $endDate = new DateTime(date('Y-m-d'));

            if ($startDate->getTimestamp() - $endDate->getTimestamp() > 0) {
                throw new ValidationException(['message' => $this->translator->trans('mattoid-daily-check-in-history.api.error.greater-than-today')]);
            }
        }

        // 查询是否已补签
        $historyResult = UserCheckinHistory::query()->where('user_id', $userId)->where('last_checkin_date', $checkinDate)->first();
        if ($historyResult) {
            throw new ValidationException(['message' => 'not_permission']);
        }

        $maxSupplementaryCheckin = $this->settings->get('mattoid-forum-checkin.max-supplementary-checkin') ?? 0;
        $checkinPosition = $this->settings->get('mattoid-forum-checkin.checkin-position') ?? 0;
        $spanDayCheckin = $this->settings->get('mattoid-forum-checkin.span-day-checkin') ?? 0;
        $checkinRange = $this->settings->get('mattoid-forum-checkin.checkin-range') ?? 0;
        $minSupplementaryDate = $this->settings->get('mattoid-forum-checkin.min-supplementary-date') ?? '';

        // 小药店签到则系统自动获取最后一次未签到数据进行补签
        $userCheckinHistory = [];
        if (isset($checkinRange) && $checkinRange) {
            $rangeEndData = date('Y-m-d',strtotime("-{$checkinRange} days",strtotime(date('Y-m-d'))));
            $userCheckinHistory = UserCheckinHistory::query()->where('user_id', $userId)->where('last_checkin_date', '>', $rangeEndData)->orderByDesc("last_checkin_date")->get();
        } else {
            $userCheckinHistory = UserCheckinHistory::query()->where('user_id', $userId)->orderByDesc("last_checkin_date")->get();
        }
        if (isset($checkinPosition) && $checkinPosition == 0) {
            // 查找最近一次漏签数据
            foreach ($userCheckinHistory as $key => $value) {
                $checkinStart = new DateTime($value->last_checkin_date);
                $checkinEnd = new DateTime(date('Y-m-d',strtotime("-{$key} days",strtotime(date('Y-m-d')))));

                if ($checkinStart->diff($checkinEnd)->days > 0) {
                    $checkinDate = $value->last_checkin_date;
                }
            }
        }

        // 允许最早补签日期
        $startDate = new DateTime($minSupplementaryDate);
        $endDate = new DateTime($checkinDate);
        if (!empty($minSupplementaryDate) && $endDate->getTimestamp() - $startDate->getTimestamp() < 0) {
            throw new ValidationException(['message' => $this->translator->trans('mattoid-daily-check-in-history.api.error.min-supplementary-date', ['day' => $minSupplementaryDate])]);
        }

        // 连续补签限制
        $checkinEndData = date('Y-m-d',strtotime("+{$maxSupplementaryCheckin} days",strtotime($checkinDate)));
        $checkinCount = UserCheckinHistory::query()->where('user_id', $userId)->where("last_checkin_date" , '>', $checkinDate)
            ->where('last_checkin_date', '<=', $checkinEndData)->where('type', 1)->count();
        if (isset($maxSupplementaryCheckin) && $maxSupplementaryCheckin > 0 && $checkinCount >= $maxSupplementaryCheckin) {
            throw new ValidationException(['message' => $this->translator->trans('mattoid-daily-check-in-history.api.error.max-supplementary-checkin', ['day' => $maxSupplementaryCheckin])]);
        }

        // 签到范围
        $startDate = new DateTime($checkinDate);
        $endDate = new DateTime(date('Y-m-d'));
        $diffDay = $endDate->diff($startDate)->days;
        if (isset($checkinRange) && $checkinRange > 0 && $diffDay > $checkinRange) {
            throw new ValidationException(['message' => $this->translator->trans('mattoid-daily-check-in-history.api.error.checkin-range', ['day' => $diffDay])]);
        }

        // 不允许跨天签到
        $totalContinuousCheckinCountHistory = 0;
        // 需要确认是连续签到，并且补签的前一天和后一天都没有漏签
        $signedinCount = UserCheckinHistory::query()->where('user_id', $userId)->where('last_checkin_date', '>', $checkinDate)->count();
        if (isset($spanDayCheckin) && !$spanDayCheckin && $signedinCount < $diffDay) {
            throw new ValidationException(['message' => $this->translator->trans('mattoid-daily-check-in-history.api.error.span-day-checkin', ['date' => $checkinDate])]);
        }
        $yesterday = date('Y-m-d',strtotime("-1 days",strtotime($checkinDate)));
        $historyResult = UserCheckinHistory::query()->where('user_id', $userId)->where('last_checkin_date', $yesterday)->first();
        if ($historyResult && $signedinCount >= $diffDay) {
            $totalContinuousCheckinCountHistory = $historyResult->total_continuous_checkin_count;
        }

        // 计算连续签补签到日期
        $day = 1;
        $userCheckinHistoryMap = [];
        $supplementaryCheckinCount = 0;
        foreach ($userCheckinHistory as $item) {
            $userCheckinHistoryMap[$item->last_checkin_date] = $item;
        }
        while (true) {
            $subDate = date('Y-m-d',strtotime("+{$day} days",strtotime($checkinDate)));
            if (empty($userCheckinHistoryMap[$subDate])) {
                break;
            }
            if ($userCheckinHistoryMap[$subDate]->type == 0) {
                break;
            }
            $day ++;
            $supplementaryCheckinCount ++;
        }

        $history = $this->event->supplementCheckin($actor, $checkinDate, $totalContinuousCheckinCountHistory, $supplementaryCheckinCount);

        return $history;
    }
}
