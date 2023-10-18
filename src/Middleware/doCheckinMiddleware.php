<?php

/*
 * This file is part of Flarum.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Mattoid\CheckinHistory\Middleware;

use Flarum\User\User;
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

            $result = json_decode($response->getBody(), true);

            $user = User::query()->find($result['data']['id']);

            $history = new UserCheckinHistory();
            $history->user_id = $result['data']['id'];
            $history->last_checkin_date = date("Y-m-d");
            $history->total_checkin_count = $user->total_checkin_count;
            $history->total_continuous_checkin_count = $result['data']['attributes']['totalContinuousCheckIn'];
            $history->last_checkin_time = $result['data']['attributes']['lastCheckinTime'];
            $history->save();
        }

        return $response;
    }
}
