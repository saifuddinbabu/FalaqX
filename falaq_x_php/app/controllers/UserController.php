<?php

/**
 * Example: UserController
 * Full CRUD for users.
 */
class UserController extends Controller
{
    private Model $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = $this->model('UserModel');
    }

    public function index(): void
    {
        $users = $this->userModel->all('created_at DESC');
        $this->render('users.index', ['title' => 'Users', 'users' => $users]);
    }

    public function show(string $id): void
    {
        $user = $this->userModel->find((int) $id);
        if (!$user) {
            http_response_code(404);
            $this->render('errors.404', ['title' => 'User Not Found']);
            return;
        }
        $this->render('users.show', ['title' => $user['name'], 'user' => $user]);
    }

    public function create(): void
    {
        $this->render('users.create', [
            'title' => 'Create User',
            'csrf'  => $this->generateCsrf(),
        ]);
    }

    public function store(): void
    {
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Invalid CSRF token.');
            $this->redirectTo('users/create');
            return;
        }

        $data = Security::sanitizeArray([
            'name'  => $this->post('name', ''),
            'email' => $this->post('email', ''),
        ]);
        $data['password'] = Security::hashPassword($this->post('password', ''));

        $id = $this->userModel->create($data);
        $this->flash('success', 'User created successfully.');
        $this->redirectTo("users/{$id}");
    }

    public function edit(string $id): void
    {
        $user = $this->userModel->find((int) $id);
        if (!$user) { $this->redirectTo('users'); return; }

        $this->render('users.edit', [
            'title' => 'Edit User',
            'user'  => $user,
            'csrf'  => $this->generateCsrf(),
        ]);
    }

    public function update(string $id): void
    {
        if (!$this->verifyCsrf()) {
            $this->redirectTo("users/{$id}/edit");
            return;
        }

        $data = Security::sanitizeArray([
            'name'  => $this->post('name', ''),
            'email' => $this->post('email', ''),
        ]);

        $this->userModel->update((int) $id, $data);
        $this->flash('success', 'User updated.');
        $this->redirectTo("users/{$id}");
    }

    public function destroy(string $id): void
    {
        $this->userModel->delete((int) $id);
        $this->flash('success', 'User deleted.');
        $this->redirectTo('users');
    }
}
