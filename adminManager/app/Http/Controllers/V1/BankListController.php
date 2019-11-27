<?php

namespace App\Http\Controllers\V1;

use App\Models\Bank\InBank;
use App\Http\Controllers\ApiController;

class BankListController extends ApiController
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
        //三方类型
        if (self::checkParameter($params, 'payId', '/^[0-9]{1,4}$/', false)) {
            return $this->replaceArray(['code' => '1016']);
        }
        $payId = 0;
        if(isset($params['payId']) && !empty($params['payId']) ){
            $payId = $params['payId'];
        }
        //银行状态
        $payBankData = InBank::getBankList((int)$payId);
        $data = [];
        if ($payBankData) {
            foreach ($payBankData as $key => $bank) {
                $data[$key]['payId'] = (int)$bank->pay_id;     //三方类型
                $data[$key]['bankName'] = (string)$bank->bank_name;  //银行名称
                $data[$key]['bankCode'] = (string)$bank->bank_code;  //code码
            }
        }

        return $this->replaceArray([
            'status' => true,
            'code' => '200',
            'data' => $data,
        ]);

    }
}