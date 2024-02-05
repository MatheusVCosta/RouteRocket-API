<?php

namespace App\Services;

use App\Interfaces\RedirectInterface;
use App\Models\Redirect;
use App\Models\RedirectLog;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\Collection;

class RedirectLogService implements RedirectInterface
{
    protected $redirectLogModel;

    public function __construct(RedirectLog $redirectLogModel)
    {
        $this->redirectLogModel = $redirectLogModel;

    }

    public function findAll() : Collection
    {
        return collect();
    }

    public function findById(int $id) : Collection
    {
        return collect();
    }

    public function create(array $params) : bool
    {   
        return !!$this->redirectLogModel->create($params);
        
    }

    public function update(string $redirectCode, array $params) : bool
    {
        return false;
    }

    public function delete(string $redirectCode) : bool
    {
        return false;
    }
}