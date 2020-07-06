<?php

namespace App\Controller;

use App\Entity\Film;
use App\Repository\CommentRepository;
use App\Repository\FilmRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FilmController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(FilmRepository $filmRepository)
    {
        return $this->render('film/index.html.twig', [
            'films' => $filmRepository->findAll()
        ]);
    }

    /**
     * @Route("/show/{id}", name="film")
     */
    public function show(Film $film, Request $request, CommentRepository $commentRepository)
    {
        // offset must be an integer no less than zero
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRepository->getCommentPaginator($film, $offset);

        return $this->render('film/show.html.twig', [
            'film' => $film,
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE)
        ]);
    }
}
