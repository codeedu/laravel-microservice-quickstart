<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;
use Illuminate\Support\Str;

abstract class DefaultModelFilter extends ModelFilter
{

    protected $sortable = [];

    public function setup()
    {
        $this->blacklistMethod('isSortable');

        $noSort = $this->input('sort', '') === '';
        if ($noSort) {
            $this->orderBy('created_at', 'DESC');
        }
    }

    public function sort($column)
    {
        if(method_exists($this, $method = 'sortBy' . Str::studly($column))){
            $this->$method();
        }

        if ($this->isSortable($column)) {
            $dir = strtolower($this->input('dir')) == 'asc' ? 'ASC' : 'DESC';
            $this->orderBy($column, $dir);
        }
    }

    protected function isSortable($column)
    {
        return in_array($column, $this->sortable);
    }

}


//endereco?sort=created_at&dir=xpto
