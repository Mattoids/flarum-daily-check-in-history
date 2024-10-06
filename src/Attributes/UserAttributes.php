<?php

namespace Mattoid\CheckinHistory\Attributes;

use Flarum\Api\Serializer\BasicUserSerializer;
use Flarum\User\User;

class UserAttributes
{
    public function __invoke(BasicUserSerializer $serializer, User $user): array
    {
        $attributes = [];
        $actor = $serializer->getActor();

        $attributes['checkinCard'] = $user->checkin_card;
        $attributes['canQueryOthersHistory'] = $serializer->getActor()->can('queryOthersHistory', $user);

        return $attributes;
    }
}
