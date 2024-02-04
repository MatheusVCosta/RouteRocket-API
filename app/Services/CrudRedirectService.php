<?php

namespace App\Services;

use App\Models\Redirect;
use Exception;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\Collection;

class CrudRedirectService 
{
    protected $redirectModel;

    public function __construct(Redirect $redirectModel)
    {
        $this->redirectModel = $redirectModel;

    }

    public function findAll() : Collection
    {
        $allRedirects = $this->redirectModel->all();
        return $allRedirects;
        // return $allRedirects->map(function (Redirect $redirect) {
        //     $status = is_null($redirect->deleted_at ) ? 'Ativado' : 'Desativado';
            
        // });
    }

    public function findById(string $redirect_code) : Redirect
    {
        return $this->redirectModel->whereCode($redirect_code)->first();
    }

    public function create(array $params) : bool
    {   
        if (!$params) {
            throw new Exception("erro");
        }
        
        $lastId = $this->redirectModel->insertGetId([
            'url_target' => $params['url_target'],
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);

        if (!$lastId) {
            throw new Exception("não criou");
        }
        
        
        $codeUpdate = ['code' => $this->_generateHashCodeById($lastId)];
        return $this->update($lastId, $codeUpdate);
    }

    public function update(string $redirect_code, array $params) : bool
    {
        if (!$params) {
            throw new Exception("erro");
        }

        $redirectUpdated = $this->findById($redirect_code);
        return $redirectUpdated->update($params);
    }

    public function delete(string $redirect_code) : bool
    {
        $redirect = $this->findById($redirect_code);
        $redirect->update(['status' => false]);
        return $redirect->delete();
    }

    private function _generateHashCodeById(
        string $value, int $minLengthHash = 10
    ) : string
    {
        $hashId = new Hashids('', $minLengthHash);
        return $hashId->encode($value);
    }
    
}