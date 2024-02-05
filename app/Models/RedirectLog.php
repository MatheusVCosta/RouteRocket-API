<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function redirect()
    {
        return $this->hasMany(Redirect::class, 'id', 'redirect_id');
    }
}
