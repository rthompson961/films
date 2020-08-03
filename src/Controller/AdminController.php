<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Message\CommentMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Registry;
use Twig\Environment;

class AdminController extends AbstractController
{
    /**
     * @Route(
     *      "/admin/comment/{action}/{id}",
     *      name="review_comment",
     *      requirements={"action"="publish|reject", "id"="\d+"}
     * )
     */
    public function reviewComment(
        string $action,
        Comment $comment,
        Registry $registry,
        EntityManagerInterface $entityManager,
        MessageBusInterface $bus,
        MailerInterface $mailer,
        string $adminEmail
    ) {
        $machine = $registry->get($comment);
        if (!$machine->can($comment, 'publish')) {
            return new Response('Comment already reviewed or not in correct state.');
        }

        $machine->apply($comment, $action);
        $entityManager->flush();

        if ($action === 'publish') {
            $bus->dispatch(new CommentMessage($comment->getId()));
            
            $mailer->send((new NotificationEmail())
                ->subject('Your comment has been approved')
                ->htmlTemplate('emails/comment_approval.html.twig')
                ->from($adminEmail)
                ->to($comment->getEmail())
                ->context(['comment' => $comment]));
        }

        return $this->render('admin/review.html.twig', [
            'transition' => $action,
            'comment' => $comment
        ]);
    }
}
