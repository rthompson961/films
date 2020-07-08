<?php

namespace App\EntityListener;

use App\Entity\Film;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class FilmEntityListener
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function prePersist(Film $film, LifecycleEventArgs $event)
    {
        $film->computeSlug($this->slugger);
    }

    public function preUpdate(Film $film, LifecycleEventArgs $event)
    {
        $film->computeSlug($this->slugger);
    }
}