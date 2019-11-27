<?php

namespace App\Http\Controllers\V1;

use App\Models\Config\PayConfig;
use App\Models\Config\PayType;
use App\Http\Controllers\ApiController;

class ConfigController extends ApiController
{
    const OPEN_THIS_API = false;

    public function runApi(&$params)
    {
        // 关闭接口对接权限
        if (self::OPEN_THIS_API) {
            return $this->replaceArray(['code' => '401']);
        }

        $where = [];

        if (self::checkParameter($params, 'limit', '/^[0-9]*$/', false)) {
            return $this->replaceArray(['code' => '1016']);
        }

        if (self::checkParameter($params, 'page', '/^[0-9]*$/', false)) {
            return $this->replaceArray(['code' => '1016']);
        }

        //三方名字
        if (!empty($params['payName'])) {
            $where['conf_name'] = $params['payName'];
        }

        if (self::checkParameter($params, 'payState', '/^[0-9]{1}$/', false)) {
            return $this->replaceArray(['code' => '1016']);
        }

        if (!empty($params['payState'])) {
            $where['is_status'] = $params['payState'];
        }

        $PayConfigData = PayConfig::getConfigRedis();

        $PayType = PayType::getPayType();

        if (isset($where['is_status']) || isset($where['conf_name'])) {
            foreach ($PayConfigData as $k => $con) {
                if (isset($where['is_status']) && isset($where['conf_name'])) {
                    if (((string)$con['conf_name'] !== (string)$where['conf_name']) || ((int)$con['is_status'] !== (int)$where['is_status'])) {
                        unset($PayConfigData[$k]);
                    }
                } elseif (!isset($where['is_status']) && isset($where['conf_name'])) {
                    if (((string)$con['conf_name'] !== (string)$where['conf_name'])) {
                        unset($PayConfigData[$k]);
                    }
                } elseif (isset($where['is_status']) && !isset($where['conf_name'])) {
                    if (((int)$con['is_status'] !== (int)$where['is_status'])) {
                        unset($PayConfigData[$k]);
                    }
                }
            }
        }
        // 查询分页时
        $countPage = '';
        $data      = [];
        if (!empty($params['limit'])) {
            if (empty($params['page']) || $params['page'] < 1) {
                $params['page'] = 1;
            }
            $count         = count($PayConfigData);
            $countPage     = ceil($count / $params['limit']); #计算总页面数
            $offset        = ($params['page'] - 1) * $params['limit'];
            $PayConfigData = array_slice($PayConfigData, $offset, $params['limit']);
        }

        //参数伪装
        foreach ($PayConfigData as $key => $config) {
            $data[$key]['payId']        = (int)$config['pay_id'] ;
            $data[$key]['payName']      = (string)$config['conf_name'] ;       // 三方名称
            $data[$key]['payModels']    = (string)$config['mod'] ;             // 三方模型
            $data[$key]['DepositState'] = (int)$config['in_state'] ;        // 入款开关
            $data[$key]['outState']     = (int)$config['out_state'] ;       // 出款开关
            $data[$key]['ipState']      = (int)$config['whitelist_state']; // 白名单
            $data[$key]['payCode']      = (string)$config['pay_code'] ;        // 扫码编码
            $data[$key]['version']      = (string)$config['version'] ;        // 扫码编码
            foreach ($PayType as $value) {
                $nameKey              = "{$value['english_name']}Url";
                $nameValue            = "{$value['english_name']}_url";
                $data[$key][$nameKey] = (string)$config[$nameValue]; // 支付网关
            }
            $data[$key]['payStatus'] = (int)$config['is_status'];       // 开关状态
            $types                   = [];
            if (isset($config['pay_type'])) {
                foreach ($config['pay_type'] as $type) {
                    $types[] = $type['type_id'];
                }
                $data[$key]['payType'] = array_values($types);
            } else {
                $data[$key]['payType'] = [];
            }
        }

        return $this->replaceArray([
            'status'    => true,
            'code'      => '200',
            'countPage' => $countPage,     // 总页数
            'data'      => array_values($data),
        ]);
    }

}
