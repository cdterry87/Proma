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
}
