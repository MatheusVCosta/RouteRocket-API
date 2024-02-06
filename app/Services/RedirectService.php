<?php

namespace App\Services;

use App\Interfaces\RedirectInterface;
use App\Models\Redirect;
use Vinkla\Hashids\Facades\Hashids;
use Exception;
use GuzzleHttp\Client;
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

    /**
    * find all redirect
    *
    * @return Collection
    */
    public function findAll(): Collection
    {
        $allRedirects = $this->redirectModel->all();
        return $allRedirects;
    }

    /**
    * Create a new redirect
    *   
    * @param array $params
    *
    * @return bool|Exception
    */
    public function create(array $params): bool| Exception
    {   
        try {
            DB::beginTransaction();
            $lastId = $this->redirectModel->insertGetId([
                'url_target' => $params['url_target'],
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ]);
    
            if (!$lastId) {
                DB::rollback();
                return new Exception("Redirect not created | making Rollback...", 500);
            }
    
            $codeHash = $this->_generateHashCodeById($lastId);
            $codeArr = ['code' => $codeHash];
            if (!$this->update($lastId, $codeArr)) {
                DB::rollback();
                return new Exception("Redirect not generatehash code | making Rollback...", 500);
            }
    
            DB::commit();
            return true;
        } catch (Exception $ex) {
            DB::rollback();
            return new Exception($ex->getMessage(), 500);
        }
       

    }

    /**
    * update redirect
    *   
    * @param string|int $identifier
    *
    * @return bool|Exception
    */
    public function update(string| int $identifier, array $params): bool| Exception
    {
        if (is_string($identifier)) {
            $identifier = $this->_decodeHashCode($identifier);
        }

        $redirectUpdated = $this->redirectModel->find($identifier);
        if (!$redirectUpdated) {
            return new Exception("Redirect informed not found", 404);
        }
        
        return $redirectUpdated->update($params);
    }

    /**
    * delete a redirect
    *   
    * @param string $redirectCode
    *
    * @return bool|Exception
    */
    public function delete(string $redirectCode): bool| Exception
    {
        $identifier = $this->_decodeHashCode($redirectCode);
        $redirect = $this->redirectModel->find($identifier);

        if (!$redirect) {
            return new Exception("Redirect informed not found", 404);
        }
        if (!$redirect->update(['status' => false])) {
            return new Exception("Error redirect status not updated to disable", 500);
        };
        if (!$redirect->delete()) {
            return new Exception("Redirect informed not deleted", 500);
        }

        return true;
    }

    /**
    * Fetch redirect stats in the last 10 days
    *   
    * @param string $redirectCode
    *
    * @return array
    */
    public function getRedirectStats(string $redirectCode): array
    {
        $id_decoded = $this->_decodeHashCode($redirectCode);
        $redirect = $this->redirectModel->with('redirectLogs')->find($id_decoded);
        if (!$redirect) {
            throw new Exception(
                "Not found redirect: <$redirectCode>"
            );
        }

        $redirectLog = $redirect->redirectLogs;
        if (!$redirectLog) {
            return [];
        }

        $last_days   = \Carbon\Carbon::today()->subDays(10);
        $totalAccess = $redirectLog->count();
        $uniqueIps   = $redirectLog->getUniqueIps()->count();

        $topReferers = $redirectLog->getTopReferer()->get()->toArray();
        // refreshing in model to make query again
        $redirectLog->refresh();

        $totalAccessInLastDays  = $redirectLog->getTotalAccessByDate($last_days)->count();
        $uniqueAccessInLastDays = $redirectLog->getUniqueIps($last_days)->count();
        
        return [
            'total'         => $totalAccess,
            'unique_ips'    => $uniqueIps,
            'topReferers'   => $topReferers,
            'last_ten_days' => [
                'total'         => $totalAccessInLastDays,
                'unique_ips'    => $uniqueAccessInLastDays,
                'date'          => $last_days->format('Y-m-d')
            ]
        ];
    }

    /**
    * Fetch logs access of redirect
    *   
    * @param string $redirectCode
    *
    * @return Collection
    */
    public function getRedirectLogs(string $redirectCode): Collection
    {
        $id_decoded = $this->_decodeHashCode($redirectCode);

        $redirect = $this->redirectModel
            ->with('redirectLogs')
            ->find($id_decoded);
        if (!$redirect) {
            throw new Exception(
                "Not found redirect: <$redirectCode>"
            );
        }

        $redirectLogs = $redirect
            ->redirectLogs()
            ->orderByDesc('last_access_at')
            ->get();

        return $redirectLogs;
    }

    /**
    * Make redirect to url target
    *   
    * @param string $redirectCode
    * @param array $queryParams

    * @return array|Exception
    */
    public function redirectByHashCode(string $redirectCode, array $queryParams): array|Exception
    {
        $client   = new Client();
        $redirect = $this->redirectModel
            ->scopeFindByCode($redirectCode)
            ->withTrashed()
            ->first();

        if (!$redirect) {
            return new Exception("Not found redirect: <$redirectCode>", 404);
        }

        if (!$redirect->status || $redirect->isDeleted()) {
            return new Exception("It's redirect is disabled: <$redirectCode>", 204);
        }

        $url_target       = $this->generateTargetUrl($queryParams, $redirect->url_target);
        $responseRedirect = redirect()->away($url_target);
        $response         = $client->get($responseRedirect->getTargetUrl());
        
        if ($response->getStatusCode() < 200 || $response->getStatusCode() > 308) {
            return new Exception("Some thing was wrong", $response->getStatusCode());
        }

        return [
            'status_code' => $response->getStatusCode(),
            'redirect_to' => $responseRedirect,
            'redirect_id' => $redirect->id
            
        ];
    }

    public function generateTargetUrl($queryParams, $url_target) 
    {
        if ($queryParams) {
            if (str_contains($url_target, '?')) {
                $targetUrlExplode = explode('?', $url_target);
                parse_str($targetUrlExplode[1], $output);
                
                foreach($queryParams as $key => $value) {
                    if (isset($output[$key])) {
                        if (!empty($queryParams[$key])) {
                            $output[$key] = $value;
                        }
                    }
                }

                $queryParams = array_merge($queryParams, $output);
                
                $queryParams = http_build_query($queryParams);
                return $targetUrlExplode[0] . "?" . $queryParams;
            }
            $queryParams = http_build_query($queryParams);
            return $url_target . "?" . $queryParams;
        }
        return $url_target;
    }

    /**
    * generate a hash bases in redirect id using Hashids Lib
    *   
    * @param string $value
    *
    * @return string
    */
    private function _generateHashCodeById(string $value): string
    {
        return Hashids::encode($value);
    }

    /**
    * decode hash code based in redirect id
    *   
    * @param string $value
    *
    * @return int
    */
    private function _decodeHashCode(string $value): int
    {
        $hashDecoded = Hashids::decode($value);
        if (empty($hashDecoded)) {
            throw new Exception(
                "Check the hash identifier of the redirect: <$value>"
            );
        }
        return $hashDecoded[0];
    }
    
}