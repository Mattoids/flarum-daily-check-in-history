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

use Flarum\Api\Controller\AbstractListController;
use Flarum\Http\RequestUtil;
use Flarum\User\Exception\PermissionDeniedException;
use Mattoid\CheckinHistory\Api\Serializer\CheckinHistorySerializer;
use Mattoid\CheckinHistory\Model\UserCheckinHistory;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class PostCheckinHistoryController extends AbstractListController
{
    public $serializer = CheckinHistorySerializer::class;

    public $include = ['warnedUser', 'addedByUser', 'hiddenByUser', 'post', 'post.discussion', 'post.user'];

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

        return $request->getUri();

        $actor = RequestUtil::getActor($request);

        app('log')->info($actor);
        app('log')->info($request->getBody());
        app('log')->info($request->getAttributes());
        app('log')->info($request->getQueryParams());
        app('log')->info($request->getRequestTarget());
        app('log')->info($request->getCookieParams());
        app('log')->info($request->getMethod());
        app('log')->info($request->getParsedBody());
        app('log')->info($request->getServerParams());
        app('log')->info($request->getUri());

        if (!$actor->can('event.view')) {
            return array();
        }

        return true;
    }
}
