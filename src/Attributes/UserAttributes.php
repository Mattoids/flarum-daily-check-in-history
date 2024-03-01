<?php

namespace Mattoid\CheckinHistory\Attributes;

use Flarum\Api\Serializer\BasicUserSerializer;
use Flarum\User\User;

class UserAttributes
{
    public function __invoke(BasicUserSerializer $serializer, User $user): array
    {
        if ($serializer->getActor()->cannot('queryOthersHistory', $user)) {
            return [];
        }

        return [
            'canQueryOthersHistory' => true,
        ];
    }
}
