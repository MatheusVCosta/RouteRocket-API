<?php

namespace App\Interfaces;

use Exception;
use Illuminate\Database\Eloquent\Collection;

interface RedirectInterface
{
    public function findAll() : Collection;

    public function create(array $params): bool|Exception;

    public function update(string $redirectCode, array $params): bool|Exception;

    public function delete(string $redirectCode): bool| Exception;
}