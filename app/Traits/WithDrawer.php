<?php

namespace App\Traits;

trait WithDrawer
{
    public $isDrawerOpen = false;

    public function openDrawer()
    {
        $this->isDrawerOpen = true;
    }

    public function closeDrawer()
    {
        $this->isDrawerOpen = false;
    }

    public function showDrawerAlert($message)
    {
        session()->flash('drawer-alert--message', $message);
    }

    public function hideDrawerAlert()
    {
        session()->forget('drawer-alert--message');
    }
}
