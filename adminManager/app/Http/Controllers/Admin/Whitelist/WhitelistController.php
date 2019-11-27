<?php

namespace App\Http\Controllers\Admin\Whitelist;

use App\Extensions\Code;
use App\Http\Controllers\AdminController;
use App\Models\Whitelist\Whitelist;
use Illuminate\Support\Facades\Redis;

class WhitelistController extends AdminController
{
    /**
     * 白名单列表
     * @return mixed
     */
    public function payWhitelistSelect()
    {
        //参数验证
        $get = self::requestParam(__FUNCTION__);
        //分页(传参：page、limit)
        Whitelist::$limit = $this->getPageOffset(self::limitParam());
        /*********************  筛选条件 *********************/
        Whitelist::$where = Whitelist::selectWhere($get);
        //排序 按照id降序
        Whitelist::$orderBy = 'id desc';
        // 渴求式查询
        Whitelist::$Craving = ['payConfigs'];
        //查询
        $payWhitelists = Whitelist::getList();
        //计数
        $count = Whitelist::getListCount();
        //销毁
        Whitelist::_destroy();
        //数据伪装
        $data = Whitelist::dataCamouflage($payWhitelists);

        self::setCount($count);
        return $this->responseJson($data);
    }

    /**
     * 新增白名单
     * @return \Illuminate\Http\JsonResponse
     */
    public function payWhitelistAdd()
    {
        //参数验证
        $post = self::requestParam(__FUNCTION__);
        Whitelist::$where['pay_ip'] = $post['payIp'];
        Whitelist::$where['pay_id'] = $post['payId'];

        if (Whitelist::getOne()) {
            return $this->responseJson(Code::ALREADY_HAVE_IP);
        }
        Whitelist::_destroy();
        /*******************  获取提交数据并伪装 *************************/
        Whitelist::$data = Whitelist::addData($post);
        //添加数据
        $result = Whitelist::addToData();
        //销毁
        Whitelist::_destroy();
        if ($result->id) {
            //添加成功返回数据伪装
            $data = Whitelist::returnDataCamouflage($result);
            Redis::del('payIpWhiteListKey'); //添加成功删除之前的缓存
            return $this->responseJson($this->translateInfo(Code::ADD_SUCCESS),$data);
        } else {
            return $this->responseJson(Code::FAIL_TO_ADD);
        }
    }

    /**
     * 修改白名单
     * @return \Illuminate\Http\JsonResponse
     */
    public function payWhitelistUpdate()
    {
        //参数验证
        $put = self::requestParam(__FUNCTION__);
        //判断条件白名单ID
        Whitelist::$where['id'] = $put['Id'];
        /*******************  获取提交数据并伪装 *************************/
        Whitelist::$data = Whitelist::saveData($put);
        //修改数据
        if (Whitelist::editToData()) {
            Redis::del('payIpWhiteListKey'); //修改成功删除之前的缓存
            //销毁
            Whitelist::_destroy();
            $time = ['updatedTime' => date('Y-m-d H:i:s')];
            return $this->responseJson($this->translateInfo(Code::UPDATE_SUCCESS), $time);
        } else {
            return $this->responseJson(Code::FAIL_TO_UPDATE);
        }
    }


}
