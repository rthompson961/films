<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $data = [
            [
                'author'   => 'Jack Torrance',
                'text'     => 'Liked it so much I took my family there on a tour of the set!',
                'email'    => 'j.torrance@overlook.com',
                'created'  => '2020-07-03 11:47:15',
                'photo'    => '8620c4d4ec7b.jpg',
                'state'    => 'published',
                'ref'      => 'shining'

            ],
            [
                'author'   => 'John Smith',
                'text'     => 'One of my favourites',
                'email'    => 'johnsmith@gmail.com',
                'created'  => '2020-07-03 14:52:38',
                'photo'    => '653babf4bbf5.jpg',
                'state'    => 'published',
                'ref'      => 'shining'

            ],
            [
                'author'   => 'Alice Jones',
                'text'     => 'I notice something new everytime',
                'email'    => 'alicejones@hotmail.com',
                'created'  => '2020-07-03 18:13:49',
                'photo'    => '653babf4bbf5.jpg',
                'state'    => 'published',
                'ref'      => 'shining'

            ],
            [
                'author'   => 'Dr Emmett Brown',
                'text'     => 'Great Scott!',
                'email'    => 'emmett@brown.edu.gov',
                'created'  => '2020-07-03 18:47:15',
                'photo'    => '6bdf32889159.jpg',
                'state'    => 'published',
                'ref'      => 'future'
            ]
        ];

        foreach ($data as $item) {
            $comment = new Comment();
            $comment->setAuthor($item['author']);
            $comment->setText($item['text']);
            $comment->setEmail($item['email']);
            $comment->setCreatedAt(new \DateTime($item['created']));
            $comment->setPhoto($item['photo']);
            $comment->setState($item['state']);
            $comment->setFilm($this->getReference('film-' . $item['ref']));

            $manager->persist($comment);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [FilmFixtures::class];
    }
}
