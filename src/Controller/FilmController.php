<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Film;
use App\Form\CommentFormType;
use App\Message\CommentMessage;
use App\Repository\CommentRepository;
use App\Repository\FilmRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Routing\Annotation\Route;

class FilmController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(FilmRepository $filmRepository)
    {
        return $this->render('film/index.html.twig');
    }

    /**
     * @Route("/show/{slug}", name="film")
     */
    public function show(
        Film $film,
        MessageBusInterface $bus,
        CommentRepository $commentRepository,
        EntityManagerInterface $entityManager,
        NotifierInterface $notifier,
        string $photoDir,
        Request $request
    ) {
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setFilm($film);

            // photo submitted
            if ($photo = $form['photofile']->getData()) {
                // random file name
                $filename = bin2hex(random_bytes(6)) . '.' . $photo->guessExtension();
                try {
                    $photo->move($photoDir, $filename);
                } catch (FileException $e) {
                    // unable to upload photo
                }
                $comment->setPhoto($filename);
            }

            $entityManager->persist($comment);
            $entityManager->flush();

            // spam check
            $context = [
                'user_ip'    => $request->getClientIp(),
                'user_agent' => $request->headers->get('user-agent'),
                'referrer'   => $request->headers->get('referer'),
                'permalink'  => $request->getUri(),
            ];

            $bus->dispatch(new CommentMessage($comment->getId(), $context));

            $notifier->send(new Notification(
                'Thank you, your comment will be posted after moderation.',
                ['browser']
            ));

            return $this->redirectToRoute('film', ['slug' => $film->getSlug()]);
        }

        // offset must be an integer no less than zero
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRepository->getCommentPaginator($film, $offset);

        return $this->render('film/show.html.twig', [
            'film' => $film,
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
            'form' => $form->createView()
        ]);
    }
}
