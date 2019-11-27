<?php

namespace App\Http\Models;


use Illuminate\Database\Eloquent\Model;

class CompanyPay extends Model
{
    protected $table = 'pay_url';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * @param $data
     * @return mixed
     */
    public static function getUrls($data)
    {
        return self::select(['long_url'])
            ->where('status', 1)
            ->where('client', $data['clientId'])
            ->where('agent', $data['site'] . $data['index'])
            ->where('url_id', $data['id'])
            ->get();
    }
}