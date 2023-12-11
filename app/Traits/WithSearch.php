<?php

namespace App\Traits;

use Livewire\WithPagination;

trait WithSearch
{
    use WithPagination;

    public $search;
    public $orderBy;
    public $orderType = 'asc';
    public $perPage = 10;
}
