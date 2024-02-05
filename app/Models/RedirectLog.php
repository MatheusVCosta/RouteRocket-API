<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RedirectLog extends Model
{
    use HasFactory;


    public $timestamps = false;

    /**
    * The attributes that should be cast.
    *
    * @var array
    */
    protected $casts = [
        'last_access_at' => 'datetime:Y-m-d H:m:s',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'redirect_id',
        'ip_address_request',
        'user_agent',
        'header_referer',
        'query_params',
        'last_access_at',
        'updated_at',
    ];

    public function scopeGetTotalAccessByDate(Builder $query, DateTime $date): void
    {
        $query->where('last_access_at', '>=', $date);
    }

    public function scopeGetUniqueIps(Builder $query, DateTime $date = null): void
    {
        $query = $query->select(DB::raw('count(ip_address_request) as total'));
        if ($date) {
            $query->where('last_access_at', '>=', $date);
        }
        
        $query->groupBy('ip_address_request')
            ->havingRaw('count(ip_address_request) = 1');
    }

    public function redirect()
    {
        return $this->hasMany(Redirect::class, 'id', 'redirect_id');
    }
}
