<?php

namespace App\Http\Models;


use Illuminate\Database\Eloquent\Model;

class PayCallbackUrl extends Model
{
    protected $table = 'pay_callback_url';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * @param array $attributes
     * @return bool|string
     */
    public static function getCallbackUrlByAgent(array $attributes)
    {
        $callUrl = self::where('agent_id', $attributes['agentId'])
            ->where('client_id', $attributes['clientId'])
            ->first();

        if (empty($callUrl)) {
            return false;
        }
        if(substr($callUrl->callback_url,0,1) != "/"){
            $callUrl->callback_url = "/{$callUrl->callback_url}";
        }

        if ($callUrl->ip && $callUrl->port) {
            $url = "http://{$callUrl->ip}:{$callUrl->port}{$callUrl->callback_url}";
        } elseif ($callUrl->site_url) {
            $url = "{$callUrl->site_url}{$callUrl->callback_url}";
        } else {
            return false;
        }

        return $url;
    }
}