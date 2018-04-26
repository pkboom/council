<?php

namespace App\Presenter;

class UserPresenter extends Presenter
{
    public function welcomeMessage()
    {
        return sprintf(
            'Welcome, %s. You signed up %s.',
            $this->user->name,
            $this->user->created_at->diffForHumans()
        );
    }
}
