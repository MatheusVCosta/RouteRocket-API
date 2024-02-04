<?php

namespace App\Services;

use App\Models\Redirect;
use Exception;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\Collection;

class RedirectLogService 
{
    protected $redirectModel;

    public function __construct(Redirect $redirectModel)
    {
        $this->redirectModel = $redirectModel;

    }

    
}