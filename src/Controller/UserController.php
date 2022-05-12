<?php

namespace App\Controller;

use App\Model\UserManager;
use App\Controller\PlanningController;
use App\Model\PlanningManager;

class UserController extends AbstractController
{
    /**
     * List users
     */
    public function index(): string
    {
        $userManager = new UserManager();
        $users = $userManager->selectAll('firstname');

        return $this->twig->render('User/index.html.twig', ['users' => $users]);
    }

    /**
     * Show informations for a specific user
     */
    public function show(int $id): string|null
    {
        if (!isset($_SESSION['user'])) {
            header("Location: /");
            return null;
        }

        //if it's not an admin
        if (!$this->isAdmin()) {
            $currentUserId = $_SESSION['user']['id'];
            // Redirect the current user to its profile page
            if ($id !== $currentUserId) {
                header("Location: /profile?id=" . $currentUserId);
                return null;
            }
        }

        $planningController = new PlanningController();
        $plannings = $planningController->showPlannings();

        $userManager = new UserManager();
        $user = $userManager->selectOneById($id);

        return $this->twig->render('User/profile.html.twig', [
            'user'      => $user,
            'plannings' => $plannings,
        ]);
    }

    /**
     * Edit a specific user
     */
    public function edit(int $id): ?string
    {
        $errors = [];
        $userManager = new UserManager();
        $user = $userManager->selectOneById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $userUpdate = array_map('trim', $_POST);

            $errors = $this->validateUser($_POST);
            if (empty($errors)) {
                // if validation is ok, we pass the password to the MD5 function
                $userUpdate['password'] = md5($userUpdate['password']);
                // add somes options
                $userUpdate['is_admin'] = false;
                $userUpdate['is_archived'] = false;
                // update and redirection
                $userManager->update($userUpdate);

                header('Location: /profile?id=' . $id);

                // we are redirecting so we don't want any content rendered
                return null;
            }
        }

        $user['is_admin'] = ($user['is_admin'] === 0) ? 'Etudiant' : 'Admin';
        return $this->twig->render('User/edit.html.twig', [
            'user' => $user,
            'errors' => $errors,
        ]);
    }

    /**
     * Add a new user
     */
    public function add(): ?string
    {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $user = array_map('trim', $_POST);
            $errors = $this->validateUser($_POST);
            if (empty($errors)) {
                // if validation is ok, we pass the password to the MD5 function
                $user['password'] = md5($user['password']);
                // add somes options
                $user['is_admin'] = false;
                $user['is_archived'] = false;
                $user['fridge_used'] = false;
                $user['promo_id'] = 1;
                // insert and redirection
                $userManager = new UserManager();
                $id = $userManager->insert($user);

                header('Location:/profile?id=' . $id);
                return null;
            }
        }
        return $this->twig->render('User/add.html.twig', ['errors' => $errors]);
    }

    /**
     * Delete a specific user
     */
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id']);
            $userManager = new UserManager();
            $userManager->delete((int)$id);

            header('Location:/users');
        }
    }

    /**
     * Form validation
     */
    public function validateUser($user): array
    {
        $errors = [];
        // Verification of the data from the form
        if (empty($user['firstname']) || strlen($user['firstname']) > 100) {
            $errors[] = 'Veuillez remplir votre prénom. Le prénom ne doit pas dépasser 100 caractères';
        }
        if (empty($user['lastname']) || strlen($user['lastname']) > 100) {
            $errors[] = 'Veuillez remplir votre nom. Le nom ne doit pas dépasser 100 caractères';
        }
        // Password check
        if (empty($user['password']) || empty($user['password2']) || ($user['password'] !== $user['password2'])) {
            $errors[] = 'Les mots de passe doivent etre renseignés et identiques';
        }
        // Email check
        $emailErrors = $this->validateEmail($user);
        // add email errors to the array
        $errors = array_merge($errors, $emailErrors);
        return $errors;
    }

    /**
     * Email validation
     */
    public function validateEmail($user): array
    {
        $errors = [];
        // is the email format correct?
        if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Veuillez saisir une adresse email valide';
        }
        // then is it unique?
        $userManager = new UserManager();
        $existingUsers = $userManager->selectAll('id');
        foreach ($existingUsers as $existingUser) {
            // provided email is the same as an existing email in the database
            // it's okay only if the email from the database and its user id  are the same as the current user
            if ($user['email'] === $existingUser['email'] && $user['id'] != $existingUser['id']) {
                $errors[] = "L'email renseigné est déjà associé à un autre étudiant";
            }
        }
        return $errors;
    }
}
