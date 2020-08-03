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
                'text'     => 'Liked it so much I took my family there!',
                'email'    => 'j.torrance@overlook.com',
                'created'  => '2020-08-05 11:47:15',
                'photo'    => '8620c4d4ec7b.jpg',
                'state'    => 'published',
                'ref'      => 'shining'

            ],
            [
                'author'   => 'John Smith',
                'text'     => 'One of my favourites',
                'email'    => 'johnsmith@gmail.com',
                'created'  => '2020-08-04 14:52:38',
                'photo'    => '653babf4bbf5.jpg',
                'state'    => 'published',
                'ref'      => 'shining'

            ],
            [
                'author'   => 'Dr Emmett Brown',
                'text'     => 'Great Scott!',
                'email'    => 'emmett@brown.edu.gov',
                'created'  => '2020-08-03 18:47:15',
                'photo'    => '6bdf32889159.jpg',
                'state'    => 'published',
                'ref'      => 'future'
            ],
            [
                'author'   => 'User',
                'text'     => 'A comment that has been published.',
                'email'    => 'user@test.com',
                'created'  => '2020-08-03 18:13:49',
                'photo'    => '653babf4bbf5.jpg',
                'state'    => 'published',
                'ref'      => 'shining'

            ],
            [
                'author'   => 'User',
                'text'     => 'A comment to be accepted.',
                'email'    => 'user@test.com',
                'created'  => '2020-08-05 12:35:48',
                'photo'    => '653babf4bbf5.jpg',
                'state'    => 'notspam',
                'ref'      => 'shining'
            ],
            [
                'author'   => 'User',
                'text'     => 'A comment to be rejected.',
                'email'    => 'user@test.com',
                'created'  => '2020-08-05 13:05:22',
                'photo'    => '653babf4bbf5.jpg',
                'state'    => 'notspam',
                'ref'      => 'shining'
            ]
        ];

        foreach ($data as $item) {
            $comment = new Comment();
            $comment->setAuthor($item['author']);
            $comment->setText($item['text']);
            $comment->setEmail($item['email']);
            $comment->setPhoto($item['photo']);
            $comment->setState($item['state']);
            $comment->setFilm($this->getReference('film-' . $item['ref']));

            $manager->persist($comment);

            // overwrite current timestamp with custom created date
            $comment->setCreatedAt(new \DateTime($item['created']));
            $manager->persist($comment);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [FilmFixtures::class];
    }
}
