<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Film;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FilmController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('film/index.html.twig', [
            'films' => $this->getDoctrine()->getRepository(Film::class)->findAll()
        ]);
    }

    /**
     * @Route("/show/{id}", name="film")
     */
    public function show(Film $film, Request $request)
    {
        $repo = $this->getDoctrine()->getRepository(Comment::class);
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $repo->getCommentPaginator($film, $offset);

        return $this->render('film/show.html.twig', [
            'film' => $film,
            'comments' => $paginator,
            'previous' => $offset - $repo::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + $repo::PAGINATOR_PER_PAGE)
        ]);
    }
}
