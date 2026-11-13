<?php

namespace App\Modules\Message\Actions\CountUnreadMessages;

final readonly class CountUnreadMessagesOutput
{
    public function __construct(public int $count) {}
}
