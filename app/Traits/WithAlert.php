<?php

namespace App\Traits;

trait WithAlert
{
    public function showAlert($message)
    {
        session()->flash('alert--message', $message);
    }

    public function hideAlert()
    {
        session()->forget('alert--message');
    }
}
