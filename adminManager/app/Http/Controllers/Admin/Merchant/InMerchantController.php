<?php

namespace App\Http\Controllers\Admin\Merchant;

use App\Extensions\Code;
use App\Http\Controllers\AdminController;
use App\Models\Merchant\InMerchant;
use App\Models\Config\PayConfig;
use Illuminate\Support\Facades\Redis;

class InMerchantController extends AdminController
{
    //入款商户列表
    public function InMerchantSelect()
    {
        $get               = self::requestParam(__FUNCTION__);
        InMerchant::$limit = $this->getPageOffset(self::limitParam());
        //排序 按照id降序
        InMerchant::$orderBy = 'merchant_id desc';
        if (self::$adminUser->view_client !== 1 && self::$adminUser->view_agent === 1) {
            //商户级  显示当前账号 所属平台线下 所有数据
            InMerchant::$where['client_id'] = self::$adminUser->client_id;
        }
        if (self::$adminUser->view_client !== 1 && self::$adminUser->view_agent !== 1) {
            //代理级  只显示当前账号代理线下数据
            InMerchant::$where['client_id'] = self::$adminUser->client_id;
            InMerchant::$where['agent_id']  = self::$adminUser->agent_id;
        }

        if (!empty($get['Id'])) {
            InMerchant::$where['merchant_id'] = $get['Id'];
        }
        if (!empty($get['clientUserId'])) {
            InMerchant::$where['client_id'] = $get['clientUserId'];
        }
        if (!empty($get['agentId'])) {
            InMerchant::$where['agent_id'] = $get['agentId'];
        }
        if (!empty($get['businessNum'])) {
            InMerchant::$where['business_num'] = $get['businessNum'];
        }
        if (!empty($get['payId'])) {
            InMerchant::$where['pay_id'] = $get['payId'];
        }
        InMerchant::$Craving = ['payWays'];
        $count               = InMerchant::getListCount();
        $InMerchants         = InMerchant::getList();
        InMerchant::_destroy();
        $data = [];
        foreach ($InMerchants as $key => $InMerchant) {
            $data[$key]['Id']           = (int)$InMerchant->merchant_id;
            $data[$key]['agentId']      = (string)$InMerchant->agent_id;
            $data[$key]['payId']        = (int)$InMerchant->pay_id;
            $data[$key]['configName']   = $InMerchant['payConfig']['conf_name']; //商户所属三方名字
            $data[$key]['isApp']        = (int)$InMerchant->is_app;
            $data[$key]['merURL']       = (string)$InMerchant->mer_url;
            $data[$key]['businessNum']  = (string)$InMerchant->business_num;
            $data[$key]['md5Key']       = (string)$InMerchant->md5_private_key;
            $data[$key]['privateKey']   = (string)$InMerchant->rsa_private_key;
            $data[$key]['publicKey']    = (string)$InMerchant->public_key;
            $data[$key]['callbackURL']  = (string)$InMerchant->callback_url;
            $data[$key]['createdTime']  = (string)$InMerchant->created_at;
            $data[$key]['updatedTime']  = (string)$InMerchant->updated_at;
            $data[$key]['status']       = (int)$InMerchant->is_status;
            $data[$key]['msgOne']       = (string)$InMerchant->message1;
            $data[$key]['msgTwo']       = (string)$InMerchant->message2;
            $data[$key]['msgThr']       = (string)$InMerchant->message3;
            $data[$key]['userLevel']    = (string)$InMerchant->user_level;
            $data[$key]['clientUserId'] = (int)$InMerchant->client_id;
            $data[$key]['clientName']   = $InMerchant->clients['client_name'];
            $data[$key]['isQuick']      = (int)$InMerchant->is_quick;
            $data[$key]['typeId']       = (int)$InMerchant->pay_way;
            $data[$key]['typeName']     = (string)$InMerchant['payWays']['type_name'];
            $data[$key]['payCode']      = (string)$InMerchant->pay_code;

        }
        self::setCount($count);
        return $this->responseJson($data);
    }

    //删除
    public function InMerchantDel()
    {

        $get = self::requestParam(__FUNCTION__);
        //设置WHERE条件 根据meichant_id 删除
        InMerchant::$where['merchant_id'] = $get['Id'];
        $result                           = InMerchant::delToData($get['Id']);
        InMerchant::_destroy();
        if ($result == 0) {
            //删除失败
            return $this->responseJson(Code::FAIL_TO_DEL);
        } else {
            //删除成功
            return $this->responseJson(Code::DELETE_SUCCESS);
        }
    }

    //更新
    public function InMerchantUpdate()
    {
        $get = self::requestParam(__FUNCTION__);

        $info = InMerchant::getOne($get['Id']);
        InMerchant::_destroy();

        InMerchant::$where['merchant_id'] = $get['Id'];
        InMerchant::$data        = InMerchant::getEditData($get);

        if ($result = InMerchant::editToData()) {
            //更新成功
            InMerchant::_destroy();
            //清除redis
            $redisKey = InMerchant::$MerchantRedisKey . $info->client_id . $info->agent_id . $info->agent_num;
            if(Redis::exists($redisKey)){
               Redis::del($redisKey);
            }
            Redis::del('PayTypeList');
            $time = ['updatedTime' => date('Y-m-d H:i:s')]; ##更新时间
            return $this->responseJson($this->translateInfo(Code::UPDATE_SUCCESS), $time);

        } else {
            //修改失败
            return $this->responseJson(Code::FAIL_TO_UPDATE);
        }
    }

    //增加数据
    public function InMerchantAdd()
    {
        $post                       = self::requestParam(__FUNCTION__);
        $addData                    = [];
        $addData['agent_id']        = isset($post['agentId']) ? $post['agentId'] : self::$adminUser->agent_id;
        $addData['client_id']       = isset($post['clientUserId']) ? $post['clientUserId'] : self::$adminUser->client_id;
        $addData['business_num']    = $post['businessNum'];
        $addData['callback_url']    = $post['callbackURL'];
        $addData['is_status']       = $post['status'];
        $addData['is_app']          = $post['isApp'];
        $addData['md5_private_key'] = isset($post['md5Key']) ? $post['md5Key'] : '';
        $addData['rsa_private_key'] = isset($post['privateKey']) ? $post['privateKey'] : '';
        $addData['public_key']      = isset($post['publicKey']) ? $post['publicKey'] : '';
        $addData['pay_id']          = $post['payId'];
        $addData['message1']        = isset($post['msgOne']) ? $post['msgOne'] : '';
        $addData['message2']        = isset($post['msgTwo']) ? $post['msgTwo'] : '';
        $addData['message3']        = isset($post['msgThr']) ? $post['msgThr'] : '';
        $addData['mer_url']         = isset($post['merURL']) ? $post['merURL'] : '';
        $addData['pay_way']         = $post['typeId'];
        $addData['pay_code']        = isset($post['payCode']) ? $post['payCode'] : '';

        InMerchant::$data = $addData; // 新增商户数据

        if ($result = InMerchant::addToData()) {
            InMerchant::_destroy();
            //对应三方当前使用数量
            $count = (int)PayConfig::getOne($result->pay_id)->count_use;
            //更新三方使用数量
            PayConfig::$data            = ['count_use' => $count + 1];
            PayConfig::$where['pay_id'] = $result->pay_id;

            if ($res = PayConfig::editToData()) {
                PayConfig::_destroy();
                if (empty($result->agent_num)) {
                    $result->agent_num = 'a';
                }
                //清除Redis
                $redisKey = InMerchant::$MerchantRedisKey . $result->client_id . $result->agent_id . $result->agent_num;
                if(Redis::exists($redisKey)){
                    Redis::del($redisKey);
                }
                Redis::del('PayTypeList');

                //数据伪装
                $data                 = [];
                $data['Id']           = $result->merchant_id;
                $data['agentId']      = $result->agent_id;
                $data['clientUserId'] = $result->client_id;
                $data['clientName']   = $result->clients->client_name;
                $data['businessNum']  = $result->business_num;
                $data['callbackURL']  = $result->callback_url;
                $data['status']       = $result->is_status;
                $data['isApp']        = $result->is_app;
                $data['md5Key']       = $result->md5_private_key;
                $data['privateKey']   = $result->rsa_private_key;
                $data['publicKey']    = $result->public_key;
                $data['payId']        = $result->pay_id;
                $data['configName']   = $result->payConfig->conf_name;
                $data['msgOne']       = $result->message1;
                $data['msgTwo']       = $result->message2;
                $data['msgThr']       = $result->message3;
                $data['merURL']       = $result->mer_url;
                $data['typeId']       = $result->pay_way;
                $data['typeName']     = $result->payWays->type_name;
                $data['payCode']      = $result->pay_code;
                return $this->responseJson($this->translateInfo(Code::ADD_SUCCESS), $data);
            }
            return $this->responseJson(Code::FAIL_TO_ADD);
        } else {
            return $this->responseJson(Code::FAIL_TO_ADD);
        }
    }

    /**
     * 返回选中三方对应拥有的支付方式
     */
    public function getOwnPayType()
    {
        $get = self::requestParam(__FUNCTION__);
        PayConfig::_destroy();
        PayConfig::$Craving = ['payTypes'];
        $typeData           = PayConfig::getOne($get['payId'])['payTypes'];

        //伪装
        $data = [];
        foreach ($typeData as $key => $val) {
            $data[$key]['typeId']   = $val->type_id;
            $data[$key]['typeName'] = $val->type_name;
        }
        return $this->responseJson($data);
    }
}