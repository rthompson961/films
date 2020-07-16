<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Film;
use App\Form\CommentFormType;
use App\Message\CommentMessage;
use App\Repository\CommentRepository;
use App\Repository\FilmRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class FilmController extends AbstractController
{
    private $bus;
    private $twig;

    public function __construct(MessageBusInterface $bus, Environment $twig)
    {
        $this->bus = $bus;
        $this->twig = $twig;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(FilmRepository $filmRepository)
    {
        $response = new Response($this->twig->render('film/index.html.twig', [
            'films' => $filmRepository->findAll()
        ]));
        $response->setSharedMaxAge(3600);

        return $response;
    }

    /**
     * @Route("/film_header", name="film_header")
     */
    public function filmHeader(FilmRepository $filmRepository)
    {
        $response = new Response($this->twig->render('film/header.html.twig', [
            'films' => $filmRepository->findAll()
        ]));
        $response->setSharedMaxAge(3600);

        return $response;
    }

    /**
     * @Route("/show/{slug}", name="film")
     */
    public function show(
        Film $film,
        Request $request,
        CommentRepository $commentRepository,
        string $photoDir
    ) {
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $comment->setFilm($film);

            // if photo submitted
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

            $this->bus->dispatch(new CommentMessage($comment->getId(), $context));

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
            'comment_form' => $form->createView()
        ]);
    }
}
