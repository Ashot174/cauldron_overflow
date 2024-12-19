<?php

namespace App\EventListener;

use App\Notification\SlackNotification;
use Sentry\State\HubInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ExceptionListener
{
    private HubInterface $sentryHub;
    private HttpClientInterface $httpClient;

    public function __construct(HubInterface $sentryHub, HttpClientInterface $httpClient)
    {
        $this->sentryHub = $sentryHub;
        $this->httpClient = $httpClient;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        //TODO Slack open comment to send notifications to slack
        $exception = $event->getThrowable();
        //$notification = new SlackNotification('Hello, Slack!');
        $this->sentryHub->captureException($exception);

        $this->httpClient->request('POST', 'https://hooks.slack.com/services/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', [
            'json' => ['text' => "<https://my-org-w2.sentry.io/issues/?project=4503980797984768|{$exception->getMessage()}>"],
        ]);

    }
}