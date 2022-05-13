<?php

namespace App\Controller;

use App\Model\CommentManager;

class CommentController extends AbstractController
{
    /**
     * List comments
     */
    public function index(): string
    {
        $commentManager = new CommentManager();
        $comments = $commentManager->selectAll('c.created_at');

        return $this->twig->render('Comment/index.html.twig', [
            'comments'      => $comments,
            'loginErrors'   => $this->loginErrors,
        ]);
    }

    /**
     * Show informations for a specific comment
     */
    public function show(int $id): string
    {
        $commentManager = new CommentManager();
        $comment = $commentManager->selectOneById($id);

        return $this->twig->render('Comment/show.html.twig', [
            'comment'       => $comment,
            'loginErrors'   => $this->loginErrors,
        ]);
    }

    /**
     * Edit a specific comment
     */
    public function edit(int $id): ?string
    {
        $errors = [] ;
        $commentManager = new CommentManager();
        $comment = $commentManager->selectOneById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $commentUpdated = array_map('trim', $_POST);

            $errors = $this->addCommentValidation($_POST);
            if (empty($errors)) {
                // if validation is ok, update and redirection
                $commentManager->update($commentUpdated);
                header('Location: /comments/show?id=' . $id);

                // we are redirecting so we don't want any content rendered
                return null;
            }
        }

        return $this->twig->render('Comment/edit.html.twig', [
            'comment'       => $comment,
            'errors'        => $errors,
            'loginErrors'   => $this->loginErrors,
        ]);
    }

    /**
     * Add a new comment
     */
    public function add(): ?string
    {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $comment = array_map('trim', $_POST);
            $errors = $this->addCommentValidation($_POST);
            if (empty($errors)) {
                // if validation is ok, insert and redirection
                $commentManager = new CommentManager();
                $id = $commentManager->insert($comment);

                header('Location:/comments/show?id=' . $id);
                return null;
            }
        }
        return $this->twig->render('Comment/add.html.twig', [
            'errors'        => $errors,
            'loginErrors'   => $this->loginErrors,
        ]);
    }

    public function addCommentValidation($post): array
    {
        $errors = [];
        if (isset($post['comment']) && empty($post['comment'])) {
            $errors[] = 'Veuillez mettre du texte dans le champ du commentaire';
        }
        return $errors;
    }

    /**
     * Delete a specific comment
     */
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id']);
            $commentManager = new CommentManager();
            $commentManager->delete((int)$id);

            header('Location:/comments');
        }
    }
}
