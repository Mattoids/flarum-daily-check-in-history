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
use Flarum\Locale\Translator;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\Exception\PermissionDeniedException;
use Flarum\User\User;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use Mattoid\CheckinHistory\Api\Serializer\CheckinHistorySerializer;
use Mattoid\CheckinHistory\Event\SupplementaryCheckinEvent;
use Mattoid\CheckinHistory\Model\UserCheckinHistory;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class PostGiveCheckinCardController extends AbstractCreateController
{
    public $serializer = CheckinHistorySerializer::class;

    public $include = ['post', 'post.discussion', 'post.user', 'user'];

    protected $translator;
    protected $settings;
    protected $events;

    public function __construct(SettingsRepositoryInterface $settings, Dispatcher $events, Translator $translator)
    {
        $this->translator = $translator;
        $this->settings = $settings;
        $this->events = $events;
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
        $username = Arr::get($request->getParsedBody(), 'username');
        $amount = Arr::get($request->getParsedBody(), 'amount');
        $range = Arr::get($request->getParsedBody(), 'range');
        $userGroup = Arr::get($request->getParsedBody(), 'userGroup');

        if (!$actor->can('checkin.issuanceOfSupplementaryCards')) {
            throw new PermissionDeniedException();
        }

        if (!$range) {
            $separatorList = [" ", ",", "，", "|"];
            $username = explode(array_pop($separatorList), $username);
            foreach ($separatorList as $separator) {
                $list = [];
                foreach ($username as $item) {
                    $list = array_merge($list, explode($separator, $item));
                }
                if ($list) {
                    $username = $list;
                }
            }

            User::query()->whereIn("username", $username)->increment("checkin_card", $amount);
        } else {
            User::query()->increment("checkin_card", $amount);
        }

//        return $history;
    }
}
