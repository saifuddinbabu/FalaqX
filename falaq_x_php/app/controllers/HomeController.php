<?php

namespace App\Controllers;

use FalaqX\Core\Controller;
use FalaqX\Helpers\Security;

/**
 * Example: HomeController
 * Handles public-facing pages.
 */
class HomeController extends Controller
{
    public function index(): void
    {
        $this->render('home.index', [
            'title'   => 'Welcome to ' . APP_NAME,
            'message' => 'FalaqX Framework is running!',
        ]);
    }

    public function about(): void
    {
        $this->render('home.about', ['title' => 'About Us']);
    }

    public function contact(): void
    {
        $csrf = $this->generateCsrf();
        $this->render('home.contact', [
            'title' => 'Contact',
            'csrf'  => $csrf,
            'flash' => $this->getFlash('success'),
        ]);
    }

    public function contactSubmit(): void
    {
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Invalid form token. Please try again.');
            $this->redirectTo('contact');
            return;
        }

        $name    = Security::sanitize($this->post('name', ''));
        $email   = Security::sanitize($this->post('email', ''));
        $message = Security::sanitize($this->post('message', ''));

        // TODO: send email, save to DB, etc.

        $this->flash('success', "Thanks {$name}, we'll be in touch!");
        $this->redirectTo('contact');
    }
}
