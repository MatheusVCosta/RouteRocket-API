<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Redirect extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
    * The attributes that should be cast.
    *
    * @var array
    */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'url_target',
        'status',
        'created_at',
        'updated_at',
    ];


    /**
    * Translate status of boolean for string
    *
    * @return string
    */
    public function isDisable()
    {
        return $this->status == 1 ? "Ativado" : "Desativado";
    }

    /**
    * check if items is deleted
    *
    * @return boolean
    */
    public function isDeleted()
    {
        return !is_null($this->deleted_at) ? true : false;
    }

    /**
    * scope to find a redirect by code
    *   
    * @param string $code
    *
    * @return $this
    */
    public function scopeFindByCode(string $code)
    {
        return $this->where('code', '=', $code);
    }

    /**
    * relationship with redirectLogs
    *
    * @return $this
    */
    public function redirectLogs()
    {
        return $this->belongsTo(RedirectLog::class, 'id', 'redirect_id');
    }
}
