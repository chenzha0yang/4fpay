<?php

namespace App\Http\Controllers\V1;

use App\Models\Config\PayType;
use App\Http\Controllers\ApiController;

class PayTypeController extends ApiController
{
    const OPEN_THIS_API = false;
    /**
     * 逻辑处理方法体，每个类中必须要有此方法
     * 银行
     * @param $params
     * @return array
     */
    public function runApi(&$params)
    {
        // 关闭接口对接权限
        if (self::OPEN_THIS_API) {
            return $this->replaceArray(['code' => '401']);
        }

        $payTypeData = PayType::getPayType();

        $data = [];
        if ($payTypeData) {
            foreach ($payTypeData as $key => $type) {
                $data[$key]['typeId']     = (int)is_array($type) ? $type['type_id'] : $type->type_id; // 支付方式ID
                $data[$key]['Name']       = (string)is_array($type) ? $type['type_name'] :$type->type_name;     // 中文名称
                $data[$key]['typeName']   = (string)is_array($type) ? $type['english_name'] : $type->english_name;  // 英文名称
                $data[$key]['typeStatus'] = (int)is_array($type) ? $type['is_status'] : $type->is_status;     // 状态
            }
        }

        return $this->replaceArray([
            'status' => true,
            'code'   => '200',
            'data'   => $data,
        ]);

    }
}