<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Extensions\Code;
use App\Extensions\Common;
use App\Extensions\RequestRule;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, Common, RequestRule;

    /**
     * 本类实例 单例
     * @var null
     */
    private static $_instances = null;

    /**
     * 登录信息
     * @var
     */
    public static $adminUser;
    /**
     * 返回数据
     * @var array
     */
    protected static $response = [
        'code'    => Code::SUCCESS,
        'count'   => 0,
        'msg'     => '',
        'C-Token' => '',
        'R-Token' => '',
        'data'    => [],
    ];
    /**
     * 返回数据
     * @var array
     */
    protected static $outResponse = [
        'status'  => true,
        'code'    => Code::SUCCESS,
        'data'    => '',
    ];

    /**
     * 配合 limitParam 使用 设置返回 count 条数
     *
     * @param int $count
     */
    protected static function setCount(int $count)
    {
        self::$response['count'] = $count;
    }

    /**
     * 系统验证规则 参数获取
     *
     * @param string $name
     * @return array
     */
    protected static function requestParam(string $name)
    {
        if (empty($name)) {
            self::$response['code'] = Code::FAIL_TO_PARAM;
            self::$response['msg'] = trans('validation.paramError');
            header('Access-Control-Allow-Origin: ' . request()->header('Origin'));
            exit(json_encode(self::$response));
        }

        if (empty(self::getInstance()->{$name . 'Rule'})) {
            return request()->all();
        }

        $validator = Validator::make(request()->all(), self::getInstance()->{$name . 'Rule'});
        if ($validator->fails()) {
            self::$response['code'] = Code::FAIL_TO_PARAM;
            self::$response['msg'] = $validator->errors()->first();

            if (is_array(self::$adminUser)) {
                self::$response['C-Token'] = encrypt(md5(self::$adminUser->id . '-' . self::$adminUser->username));
                self::$response['R-Token'] = self::$adminUser->remember_token;
            }
            header('Access-Control-Allow-Origin: ' . request()->header('Origin'));
            exit(json_encode(self::$response));
        }

        return $validator->getData();
    }

    /**
     * 统一数据返回
     *
     * @param $args
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseJson(...$args)
    {
        foreach ($args as $arg) {
            if (is_int($arg)) {
                self::$response['code'] = $arg;
            } elseif (is_string($arg)) {
                self::$response['msg'] = $arg;
            } else {
                self::$response['data'] = $arg;
            }
        }

        if (empty(self::$response['msg'])) {
            self::$response['msg'] = $this->translateInfo(self::$response['code']);
        }

        return response()->json(self::$response);
    }

    /**
     * 获取此类单例
     *
     * @return Controller|null
     */
    protected static function getInstance()
    {
        if (!empty(self::$_instances)) {
            return self::$_instances;
        }

        return self::$_instances = new static();
    }
}
