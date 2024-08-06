<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class TurboFrameRedirectSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ResponseEvent::class => 'onKernelResponse',
        ];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$this->shouldWrapRedirect($event->getRequest(), $event->getResponse())) {
            return;
        }

        // if we have the custom turbo frame redirect header, change the response
        // see assets/app.js for more information
        $response = new Response(null, Response::HTTP_NO_CONTENT, [
            'Turbo-Location' => $event->getResponse()->headers->get('Location'),
        ]);
        $event->setResponse($response);
    }

    private function shouldWrapRedirect(Request $request, Response $response): bool
    {
        if (!$response->isRedirection()) {
            return false;
        }
        return (bool) $request->headers->get('Turbo-Frame-Redirect');
    }
}
