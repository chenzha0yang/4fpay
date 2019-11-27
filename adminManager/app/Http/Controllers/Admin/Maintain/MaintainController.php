<?php

namespace App\Http\Controllers\Admin\Maintain;

use App\Extensions\Code;
use App\Http\Controllers\AdminController;
use App\Models\Maintain\Maintain;

class MaintainController extends AdminController
{
    public function MaintainSelect()
    {
        $get             = self::requestParam(__FUNCTION__);
        Maintain::$limit = $this->getPageOffset(self::limitParam());
        if (!empty($get['payId'])) {
            Maintain::$where['pay_id'] = $get['payId'];
        }
        Maintain::$Craving = ['payConfig'];
        $count             = Maintain::getListCount();
        $lists             = Maintain::getList();
        Maintain::_destroy();
        $data = [];
        foreach ($lists as $key => $val) {
            $data[$key]['Id']          = (int)$val->id;
            $data[$key]['state']       = (int)$val->maintain;
            $data[$key]['projectType'] = (int)$val->project;
            $data[$key]['payId']       = (int)$val->pay_id;
            $data[$key]['confName']    = (string)$val->payConfig['conf_name'];
            $data[$key]['msg']         = (string)$val->message;
            $data[$key]['createdTime'] = (string)$val->created_at;
            $data[$key]['updatedTime'] = (string)$val->updated_at;
        }
        self::setCount($count);
        return $this->responseJson($data);
    }

    //更新
    public function MaintainUpdate()
    {
        $get                    = self::requestParam(__FUNCTION__);
        Maintain::$where['id']  = $get['Id'];
        $editData               = [];
        if (isset($get['state'])) {
            $editData['maintain']   = $get['state'];
        }
        if (isset($get['msg'])) {
            $editData['message']   = $get['msg'];
        }
        $time = date('Y-m-d H:i:s');
        Maintain::$data         = $editData;
        $result                 = Maintain::editToData();
        Maintain::_destroy();
        if ($result == 0) {
            //修改失败
            return $this->responseJson(Code::FAIL_TO_UPDATE);
        } else {
            //更新成功
            return $this->responseJson($this->translateInfo(Code::UPDATE_SUCCESS), $time);
        }
    }

}