<?php

namespace App\Http\Controllers\Admin\Merchant;

use App\Extensions\Code;
use App\Http\Controllers\AdminController;
use App\Models\Merchant\OutMerchant;
use Doctrine\DBAL\Schema\AbstractAsset;

class OutMerchantController extends AdminController
{
    //出款商户列表
    public function outMerchantSelect()
    {
        $get                = self::requestParam(__FUNCTION__);
        OutMerchant::$limit = $this->getPageOffset(self::limitParam());
        //查询条件

        if (self::$adminUser->view_client !== 1 && self::$adminUser->view_agent === 1) {
            //商户级  显示当前账号 所属平台线下 所有数据
            OutMerchant::$where['client_id'] = self::$adminUser->client_id;
        }
        if (self::$adminUser->view_client !== 1 && self::$adminUser->view_agent !== 1) {
            //代理级  只显示当前账号代理线下数据
            OutMerchant::$where['client_id'] = self::$adminUser->client_id;
            OutMerchant::$where['agent_id']  = self::$adminUser->agent_id;
        }

        if (!empty($get['Id'])) {
            OutMerchant::$where['merchant_id'] = $get['Id'];
        }
        if (!empty($get['clientUserId'])) {
            OutMerchant::$where['client_id'] = $get['clientUserId'];
        }
        if (!empty($get['agentId'])) {
            OutMerchant::$where['agent_id'] = $get['agentId'];
        }
        if (!empty($get['businessNum'])) {
            OutMerchant::$where['business_num'] = $get['businessNum'];
        }
        if (!empty($get['payId'])) {
            OutMerchant::$where['pay_id'] = $get['payId'];
        }
        OutMerchant::$Craving = ['payConfig'];
        $count        = OutMerchant::getListCount();
        $outMerchants = OutMerchant::getList();
        OutMerchant::_destroy();
        $data = [];
        foreach ($outMerchants as $key => $outMerchant) {
            $data[$key]['Id']  = (int)$outMerchant->merchant_id;
            $data[$key]['agentId']     = (string)$outMerchant->agent_id;
            $data[$key]['payId']       = (int)$outMerchant->pay_id;
            $data[$key]['configName']   = $outMerchant['payConfig']['conf_name']; //商户所属三方名字
            $data[$key]['isApp']       = (int)$outMerchant->is_app;
            $data[$key]['merURL']      = (string)$outMerchant->mer_url;
            $data[$key]['businessNum'] = (string)$outMerchant->business_num;
            $data[$key]['md5Key']      = (string)$outMerchant->md5_private_key;
            $data[$key]['privateKey']  = (string)$outMerchant->private_key;
            $data[$key]['publicKey']   = (string)$outMerchant->public_key;
            $data[$key]['callbackURL'] = (string)$outMerchant->callback_url;
            $data[$key]['createdTime'] = (string)$outMerchant->created_at;
            $data[$key]['updatedTime'] = (string)$outMerchant->updated_at;
            $data[$key]['status']      = (int)$outMerchant->is_status;
            $data[$key]['msgOne']      = (string)$outMerchant->message1;
            $data[$key]['msgTwo']      = (string)$outMerchant->message2;
            $data[$key]['msgThr']      = (string)$outMerchant->message3;
            $data[$key]['userLevel']   = (string)$outMerchant->user_level;
            $data[$key]['clientUserId']    = (int)$outMerchant->client_id;
            $data[$key]['clientName']   = $outMerchant->clients['client_name'];
            $data[$key]['isQuick']     = (int)$outMerchant->is_quick;
        }
        self::setCount($count);
        return $this->responseJson($data);
    }

    //删除
    public function outMerchantDel()
    {
        $del = self::requestParam(__FUNCTION__);
        //设置WHERE条件 根据meichant_id 删除
        OutMerchant::$where['merchant_id'] = $del['Id'];
        $result                            = OutMerchant::delToData();
        OutMerchant::_destroy();
        if ($result == 0) {
            //删除失败
            return $this->responseJson(Code::FAIL_TO_DEL);
        } else {
            //删除成功
            return $this->responseJson(Code::DELETE_SUCCESS);
        }
    }

    //更新
    public function outMerchantUpdate()
    {
        $update                            = self::requestParam(__FUNCTION__);
        OutMerchant::$where['merchant_id'] = $update['Id'];
        OutMerchant::$data = OutMerchant::getEditData($update);
        $result            = OutMerchant::editToData();
        $time = ['updatedTime' => date('Y-m-d H:i:s')]; ##更新时间

        OutMerchant::_destroy();
        if ($result == 0) {
            //修改失败
            return $this->responseJson(Code::FAIL_TO_UPDATE);
        } else {
            //更新成功
            return $this->responseJson($this->translateInfo(Code::UPDATE_SUCCESS), $time);
        }
    }

    //增加数据
    public function outMerchantAdd()
    {
        $get                        = self::requestParam(__FUNCTION__);
        $addData                    = [];
        $addData['agent_id']        = isset($get['agentId']) ? $get['agentId'] : self::$adminUser->agent_id;
        $addData['agent_num']       = 'a';
        $addData['client_id']       = isset($get['clientUserId']) ? $get['clientUserId'] : self::$adminUser->client_id;
        $addData['business_num']    = $get['businessNum'];
        $addData['callback_url']    = $get['callbackURL'];
        $addData['is_status']       = $get['status'];
        $addData['md5_private_key'] = isset($get['md5Key']) ? $get['md5Key'] : '';
        $addData['private_key']     = isset($get['privateKey']) ? $get['privateKey'] : '';
        $addData['public_key']      = isset($get['publicKey']) ? $get['publicKey'] : '';
        $addData['pay_id']          = $get['payId'];
        OutMerchant::$data          = $addData;
        $result                     = OutMerchant::addToData();
        OutMerchant::_destroy();
        if ($result->merchant_id) {
            $data                 = [];
            $data['Id']           = $result->merchant_id;
            $data['agentId']      = $result->agent_id;
            $data['clientUserId'] = $result->client_id;
            $data['businessNum']  = $result->business_num;
            $data['callbackURL']  = $result->callback_url;
            $data['status']       = $result->is_status;
            $data['md5Key']       = $result->md5_private_key;
            $data['privateKey']   = $result->rsa_private_key;
            $data['publicKey']    = $result->public_key;
            $data['payId']        = $result->pay_id;
            return $this->responseJson(Code::ADD_SUCCESS,[$data]);
        } else {
            return $this->responseJson(Code::FAIL_TO_ADD);
        }
    }
}