<?php

namespace App\Notification;

use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\RecipientInterface;

class SlackNotification extends Notification
{
    public function __construct(string $content)
    {
        $this->content = $content;

        parent::__construct();
    }

    public function getChannels(RecipientInterface $recipient): array
    {
        return ['slack'];
    }

    public function asSlackMessage(): string
    {
        return $this->content;
    }
}