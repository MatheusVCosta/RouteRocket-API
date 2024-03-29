<?php

namespace App\Services;

use App\Interfaces\RedirectInterface;
use App\Models\Redirect;
use App\Models\RedirectLog;
use Exception;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\Collection;

class RedirectLogService
{
    /**
    * redirect log model
    *
    * @var $redirectLogModel
    */
    protected $redirectLogModel;

    public function __construct(RedirectLog $redirectLogModel)
    {
        $this->redirectLogModel = $redirectLogModel;

    }

    /**
    * create a new redirectLog
    * 
    * @param array $param 
    *
    * @return $bool
    */
    public function create(array $params): bool
    {   
        $created = $this->redirectLogModel->create($params);
        if (!$created) {
            return new Exception("Redirect log not created", 500);
        }
        return !!$created;
        
    }
}