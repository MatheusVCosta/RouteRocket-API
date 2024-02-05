<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Collection;

interface RedirectInterface
{
    public function findAll() : Collection;

    public function findById(int $id);

    public function create(array $params) : bool;

    public function update(string $redirectCode, array $params) : bool;

    public function delete(string $redirectCode) : bool;
}