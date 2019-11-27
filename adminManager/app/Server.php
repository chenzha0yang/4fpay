<?php

namespace App;

use Illuminate\Support\Facades\Log;
use Request;
use Illuminate\Support\Facades\Validator;

class Server
{
    /**
     * 请求参数
     * @var array
     */
    protected $params = [];

    /**
     * API请求Method名
     * @var string
     */
    protected $method;

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
     * 初始化
     * @param Error $error Error对象
     */
    public function __construct(Error $error)
    {
        $this->params = Request::all();
        if (env('API_LOG_INFO')){
            Log::info($this->params);
        }
        $this->error  = $error;
    }

    /**
     * api服务入口执行
     * @param $className
     * @return response
     */
    public function run($className)
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
        $this->secret = $this->params['clientSecret'];

        // B. userId校验
        $client = AdminClient::getInstance($this->userId)->info();

        if (! $client) {
            return $this->response(['status' => false, 'code' => '1002']);
        }
        $this->params['clientId'] = $client->user_id; // 线路 user_id
        // C. 授权证书校验
        if ($this->secret !== $client->secret) {
            return $this->response(['status' => false, 'code' => '1002']);
        };

        // D. 判断类名是否存在
        if (!$className || !class_exists($className)) {
            return $this->response(['status' => false, 'code' => '500']);
        }

        global $app;
        $className = $app->make($className);

        // e. api接口分发
        return $this->response((array) $className->runApi($this->params));
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
