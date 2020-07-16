<?php

namespace App\EventSubscriber;

use App\Repository\FilmRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

class TwigEventSubscriber implements EventSubscriberInterface
{
    private $twig;
    private $conferenceRepository;

    public function __construct(Environment $twig, FilmRepository $filmRepository)
    {
        $this->twig = $twig;
        $this->filmRepository = $filmRepository;
    }

    public function onKernelController(ControllerEvent $event)
    {
        $this->twig->addGlobal('films', $this->filmRepository->findAll());
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.controller' => 'onKernelController',
        ];
    }
}
