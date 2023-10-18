<?php

/*
 * This file is part of Flarum.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Mattoid\CheckinHistory\Middleware;

use Illuminate\Mail\Transport\LogTransport;
use Mattoid\CheckinHistory\Model\UserCheckinHistory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class doCheckinMiddleware extends LogTransport implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        // 签到成功，记录签到数据
        if ($request->getAttribute("routeName") === 'users.update' && strpos($request->getUri()->getPath(), '/users/') !== false
            && $response->getStatusCode() === 200 && isset($request->getParsedBody()["data"]['attributes']['canCheckin'])
            && isset($request->getParsedBody()["data"]['attributes']['totalContinuousCheckIn'])) {

            $history = new UserCheckinHistory();
            $history->user_id = 1;
            $history->last_checkin_date = date("Y-m-d");
            $history->total_checkin_count = 1;
            $history->total_continuous_checkin_count = 1;
            $history->save();
        }

        return $response;
    }
}
