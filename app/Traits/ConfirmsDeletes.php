<?php

namespace App\Traits;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\ConfirmPassword;

trait ConfirmsDeletes
{
    public $confirmingDelete = false;
    public $confirmableId = null;

    public function startConfirmingDelete(string $confirmableId)
    {
        $this->confirmingDelete = true;
        $this->confirmableId = $confirmableId;

        $this->dispatch('confirming-delete');
    }

    public function confirmDelete()
    {
        $this->dispatch(
            'delete-confirmed',
            id: $this->confirmableId,
        );

        $this->stopConfirmingDelete();
    }

    public function stopConfirmingDelete()
    {
        $this->confirmingDelete = false;
        $this->confirmableId = null;
    }
}
