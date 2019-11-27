<?php

namespace App\Http\Controllers\Admin\Check;

use App\Http\Controllers\ApiController;
use App\Extensions\Code;
use Illuminate\Http\Request;
use App\Models\Config\ApiClients;

class CheckClientController extends ApiController
{
    public static function checkClient(Request $request)
    {

        $check = [
            'status' => false,
            'code'   => Code::NO_CLIENT
        ];
        $id = $request->clientUserId;
        ApiClients::$where['user_id'] = $id;
        $client = ApiClients::getOne();
        ApiClients::_destroy();
        //是否存在该客户
        if (!$client) {
            return self::getInstance()->outResponseJson($check['code']);
        }
        //状态是否开启
        if ($client->revoked !== 1) {
            $check['code'] = Code::LINE_CLOSE;
            return self::getInstance()->outResponseJson($check['code']);
        }
        //客户名称验证
        if ($client->client_name != $request->clientName) {
            $check['code'] = Code::NO_CLIENT_NAME;
            return self::getInstance()->outResponseJson($check['code']);
        }
        //证书验证
        if ($client->secret != $request->clientSecret) {
            $check['code'] = Code::FAIL_TO_SECRET;
            return self::getInstance()->outResponseJson($check['code']);
        }
        return true;
    }
}
