<?php

namespace App\Services;

use App\Interfaces\RedirectInterface;
use App\Models\Redirect;
use Exception;
use GuzzleHttp\Client;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class RedirectService implements RedirectInterface
{
    protected $redirectModel;
    protected $hashId;

    public function __construct(Redirect $redirectModel)
    {
        $this->redirectModel = $redirectModel;
        $this->hashId = new Hashids('', 10);
    }

    public function findAll() : Collection
    {
        $allRedirects = $this->redirectModel->all();
        return $allRedirects;
    }

    public function findById(int $id): Redirect
    {
        return $this->redirectModel->find($id);
    }

    public function create(array $params) : bool
    {   
        try {
            DB::beginTransaction();
            $lastId = $this->redirectModel->insertGetId([
                'url_target' => $params['url_target'],
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ]);

            if (!$lastId) {
                throw new Exception("não criou");
            }

            $codeHash = $this->_generateHashCodeById($lastId);
            $codeArr = ['code' => $codeHash];

            if (!$this->update($lastId, $codeArr)) {
                DB::rollback();
                return false;
            }

            DB::commit();
            return true;

        } catch(Exception $ex) {
            DB::rollback();
            throw new Exception($ex);
        }
    }

    public function update(string|int $identifier, array $params) : bool
    {
        if (is_string($identifier)) {
            $identifier = $this->_decodeHashCode($identifier);
        }

        $redirectUpdated = $this->findById($identifier);
        return $redirectUpdated->update($params);
    }

    public function delete(string $redirectCode) : bool
    {
        $redirect = $this->findById($this->_decodeHashCode($redirectCode));
        $redirect->update(['status' => false]);
        return $redirect->delete();
    }

    public function redirectByHashCode(string $hash_code) : array
    {
        $client   = new Client();
        $redirect = $this->redirectModel
            ->scopeFindByCode($hash_code)
            ->withTrashed()
            ->first();

        if (!$redirect) {
            return [
                'status_code' => 404,
                'redirect_to' => false,
                'message'     => 'Redirect not found'
            ];
        }
        if (!$redirect->status || $redirect->isDeleted()) {
            return [
                'status_code' => 204,
                'redirect_to' => false,
                'message'     => 'redirect with success'
            ];
        }

        $responseRedirect = redirect()->away($redirect->url_target);
        $response         = $client->get($responseRedirect->getTargetUrl());
        
        if ($response->getStatusCode() != 200) {
            return [
                'status_code' => $response->getStatusCode(),
                'redirect_to' => false,
                'message'     => 'redirect with success'
            ];
        }

        return [
            'status_code' => $response->getStatusCode(),
            'message'     => 'redirect with success',
            'redirect_to' => $responseRedirect,
            'redirect_id' => $redirect->id
            
        ];
    }


    private function _generateHashCodeById(string $value) : string
    {
        return $this->hashId->encode($value);
    }

    private function _decodeHashCode(string $value) : int
    {
        $hashDecoded = $this->hashId->decode($value);
        return $hashDecoded[0];
    }
    
}