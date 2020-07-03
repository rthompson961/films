<?php

namespace App\DataFixtures;

use App\Entity\Film;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class FilmFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $data = [
            [
                'ref'     => 'shining',
                'title'   => 'The Shining',
                'year'    => '1980',
                'showing' => false
            ],
            [
                'ref'     => 'future',
                'title'   => 'Back to the Future',
                'year'    => '1985',
                'showing' => true
            ],
        ];

        foreach ($data as $item) {
            $film = new Film();
            $film->setTitle($item['title']);
            $film->setYear($item['year']);
            $film->setIsShowing($item['showing']);

            $manager->persist($film);
            $this->addReference('film-' . $item['ref'], $film);
        }
        $manager->flush();
    }
}
