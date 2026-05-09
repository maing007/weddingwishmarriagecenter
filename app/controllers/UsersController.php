<?php

class UsersController extends Controller
{
    public function index(): void
    {
        $users = User::all();

        $this->view('users/index', [
            'title' => 'Users',
            'users' => $users
        ]);
    }
}
