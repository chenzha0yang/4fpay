<?php

namespace App\Http\Controllers\V2;


use App\Http\Controllers\APIController;
use App\Http\Models\PayMaintain;

class MaintainController extends APIController
{
    /**
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public static function getApiMaintain()
    {
        $where = [];
        $where['project'] = 1;
        return self::maintainResponse($where);
    }

    /**
     * @param $payId
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public static function getPayIdMaintain($payId)
    {
        $where = [];
        $where['project'] = 3;
        $where['pay_id'] = (int)$payId;
        return self::maintainResponse($where);
    }

    /**
     * @param array $where
     * @return bool|\Illuminate\Http\JsonResponse
     */
    private static function maintainResponse(array $where)
    {
        $maintain = PayMaintain::getMaintainData($where);

        if ($maintain['maintain'] == 1) {
            self::$response['code'] = 400;
            self::$response['data'] = $maintain->message;
            return response()->json(self::$response);
        }

        return false;
    }
}