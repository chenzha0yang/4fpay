<?php

namespace App\Http\Controllers;

use App\Extensions\Common;
use Illuminate\Http\Request;

class APIController extends Controller
{
    use Common;

    protected static $PKData = [];

    protected static $client;

    private static $_instances = null;

    protected static $response = [
        'status' => false,
        'code'   => 1014,
        'data'   => '',
    ];

    public function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * 参数验证
     *
     * @param Request $request
     * @param string $key
     * @param string $pattern
     * @param bool $notNull
     * @param bool $isData
     * @return array|string
     */
    public static function checkParameter(Request $request, $key, $pattern = '', $notNull = true, $isData = true)
    {
        $value = '';
        if ($request->has($key)) {
            $value = $request->input($key);
            if ($notNull && !$value) {
                exit(self::getInstance()->responseJson(trans("error.{$key}Error")));
            }
            if (!empty($pattern) && !preg_match($pattern, $value)) {
                exit(self::getInstance()->responseJson(trans("error.{$key}Error")));
            }
            if ($isData) {
                self::$PKData[$key] = $value;
            }
        }
        return $value;

    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseJson($data)
    {
        self::$response['data'] = $data;
        return response()->json(self::$response);
    }

    /**
     * @return APIController|null
     */
    protected static function getInstance()
    {
        if (!empty(self::$_instances)) {
            return self::$_instances;
        }

        return self::$_instances = new self();
    }

}