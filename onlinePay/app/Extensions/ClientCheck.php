<?php

namespace App\Extensions;

use App\Client;
use App\Error;
use Illuminate\Support\Facades\Validator;

class ClientCheck
{
    /**
     * 请求参数
     * @var array
     */
    protected $params = [];

    /**
     * usName
     * @var string
     */
    protected $usName;

    /**
     * userId
     * @var string
     */
    protected $userId;

    /**
     * secret
     * @var string
     */
    protected $secret;

    /**
     * 回调数据格式
     * @var string
     */
    protected $format = 'json';

    /**
     * 是否输出错误码
     * @var boolean
     */
    protected $error_code_show = false;

    /**
     * Error对象
     * @var Error
     */
    protected $error;

    /**
     * CheckClientController constructor.
     * @param $request
     */
    public function __construct($request)
    {
        $this->params = $request->all();
        $this->error  = new Error();
    }

    /**
     * Api 验证
     * @return response|bool
     */
    public function checkClient()
    {
        // A.1 初步校验
        $rules    = [
            'clientUserId' => 'required',
            'format'       => 'in:,json',
            'clientName'   => 'required',
            'clientSecret' => 'required|string',
        ];
        $messages = [
            'clientUserId.required' => '1001',
            'format.in'             => '1004',
            'clientName.in'         => '1005',
            'clientSecret.required' => '1010',
            'clientSecret.string'   => '1011',
        ];

        $v = Validator::make($this->params, $rules, $messages);
        if ($v->fails()) {
            return $this->response(['status' => false, 'code' => $v->messages()->first()]);
        }

        // A.2 赋值对象
        $this->format = !empty($this->params['format']) ? $this->params['format'] : $this->format;
        $this->userId = $this->params['clientUserId'];
        $this->usName = $this->params['clientName'];
        $this->secret = $this->params['clientSecret'];

        // B. userId校验
        $client = Client::getInstance($this->userId)->info();

        if (! $client) {
            return $this->response(['status' => false, 'code' => '1002']);
        }

        // C.name校验
        if ($this->usName != $client->client_name) {
            return $this->response(['status' => false, 'code' => '1005']);
        }

        // D. 授权证书校验
        if ($this->secret !== $client->secret) {
            return $this->response(['status' => false, 'code' => '1003']);
        }

        return true;

    }

    /**
     * 获取客户端IP地址
     * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
     * @return mixed
     */
    protected function getClientIp($type = 0,$adv=false) {
        $type       =  $type ? 1 : 0;
        static $ip  =   NULL;
        if ($ip !== NULL) return $ip[$type];
        if($adv){
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos    =   array_search('unknown',$arr);
                if(false !== $pos) unset($arr[$pos]);
                $ip     =   trim($arr[0]);
            }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip     =   $_SERVER['HTTP_CLIENT_IP'];
            }elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip     =   $_SERVER['REMOTE_ADDR'];
            }
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u",ip2long($ip));
        $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }

    /**
     * 输出结果
     * @param  array $result 结果
     * @return response|bool
     */
    protected function response(array $result)
    {
        if (! array_key_exists('data', $result) && array_key_exists('code', $result)) {
            $result['data'] = $this->getError($result['code']);
        }

        if ($this->format == 'json') {
            return response()->json($result);
        }

        return false;
    }

    /**
     * 返回错误内容
     * @param  string $code 错误码
     * @return string
     */
    protected function getError($code)
    {
        return $this->error->getError($code, $this->error_code_show);
    }

}