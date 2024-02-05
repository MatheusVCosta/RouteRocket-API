<?php

namespace App\Services;

use App\Interfaces\RedirectInterface;
use App\Models\Redirect;
use DateTime;
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
        
        // refreshing in model to make query again
        $redirectLog->refresh();

        $totalAccessInLastDays  = $redirectLog->getTotalAccessByDate($last_days)->count();
        $uniqueAccessInLastDays = $redirectLog->getUniqueIps($last_days)->count();
        
        return [
            'total'         => $totalAccess,
            'unique_ips'    => $uniqueIps,
            'last_ten_days' => [
                'total'         => $totalAccessInLastDays,
                'unique_ips'    => $uniqueAccessInLastDays,
                'date'          => $last_days->format('Y-m-d')
            ]
        ];
    }

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

        if ($queryParams) {
            if (str_contains($redirect->url_target, '?')) {
                $targetUrlExplode = explode('?', $redirect->url_target);
                parse_str($targetUrlExplode[1], $output);
                $queryParams = array_merge($queryParams, $output);
            }
            $queryParams = http_build_query($queryParams);
        
            $redirect->url_target = $redirect->url_target . "?" . $queryParams;
        }

        
        $responseRedirect = redirect()->away($redirect->url_target);
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


    private function _generateHashCodeById(string $value): string
    {
        return $this->hashId->encode($value);
    }

    private function _decodeHashCode(string $value): int
    {
        $hashDecoded = $this->hashId->decode($value);
        if (empty($hashDecoded)) {
            throw new Exception(
                "Check the hash identifier of the redirect: <$value>"
            );
        }
        return $hashDecoded[0];
    }
    
}