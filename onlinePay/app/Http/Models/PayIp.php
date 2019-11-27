<?php

namespace App\Http\Models;

use App\Extensions\RedisConPool;
use \Illuminate\Database\Eloquent\Model;

class PayIp extends Model
{
    protected $table = 'pay_ip_whitelist';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public static function GetPayIpByIp($Type, $ip)
    {

        if (empty($Type)) {
            return false;
        }
        $payType = RedisConPool::get('payIpWhiteListKey');
        if (empty($payType)) {
            // redis中数据为空 从数据库读取
            $data = self::getPayTypeIp();
            RedisConPool::set('payIpWhiteListKey', json_encode($data));
        } else {
            $data = json_decode($payType);
        }
        if (empty($data)) {
            return false;
        }

        foreach ($data as $val) {
            if ($val->pay_id == $Type && $val->pay_ip == $ip) {
                return true;
            }
        }
        return false;
    }

    //获取ip
    public static function getPayTypeIp()
    {
        $data = self::where('ip_state', 1)
            ->orderBy('id', 'ASC')
            ->get();
        $array = array();
        foreach ($data as $k => $v) {
            $array[$v->id] = $v;
        }
        return $array;
    }

    /**
     * 将ip地址转换成int型
     * bindec(decbin(ip2long('这里填ip地址')));
     * ip2long();的意思是将IP地址转换成整型 ，
     * 之所以要decbin和bindec一下是为了防止IP数值过大int型存储不了出现负数。
     *
     * @param $ip  ip地址
     * @return number 返回数值
     */
    public static function getIpLong($ip)
    {
        return bindec(decbin(ip2long($ip)));
    }
}