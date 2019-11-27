<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    /**
     * 表名
     * @var string
     */
    protected $table = 'api_clients';


    /**
     *
     */
    public static function getClientKey($clientId)
    {
        return self::where('revoked', 1)
            ->where('user_id', $clientId)
            ->first();
    }
}