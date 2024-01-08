<?php

namespace App\Traits;

use Livewire\WithPagination;

trait WithFiltersAndSorting
{
    use WithPagination;

    public $perPage = 15;
    public $filters = [];
    public $sortByOptions = [];
    public $sortBy;
    public $sortType;
    public $search;
}
