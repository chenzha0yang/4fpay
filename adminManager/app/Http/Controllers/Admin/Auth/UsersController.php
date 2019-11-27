<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Extensions\Code;
use App\Http\Controllers\AdminController;
use App\Models\Auth\Role;
use App\Models\Auth\Users;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Extensions\RedisConPool;

class UsersController extends AdminController
{
    public function usersSelect()
    {
        $get          = self::requestParam(__FUNCTION__);
        Users::$limit = $this->getPageOffset(self::limitParam());
        //查询条件
        if (!empty($get['name'])) {
            Users::$where['name'] = $get['name'];
        }
        if (!empty($get['account'])) {
            Users::$where['username'] = $get['account'];
        }

        //按平台、代理线路查询
        if (self::$adminUser->client_id !== 0 && self::$adminUser->agent_id === '0') {
            //平台线路管理员
            Users::$where[] = ['client_id', '=', self::$adminUser->client_id];
            Users::$where[] = ['agent_id', '<>', '0'];
        }
        if (self::$adminUser->client_id !== 0 && self::$adminUser->agent_id !== '0') {
            //代理线路管理员
            Users::$where[] = ['id', '=', self::$adminUser->id];  //只能看见自己账号
        }
        $count          = Users::getListCount();
        Users::$Craving = ['roles'];
        $lists          = Users::getList();
        Users::_destroy();
        $data = [];
        foreach ($lists as $key => $val) {
            $data[$key]['Id']            = (int)$val->id;
            $data[$key]['account']       = (string)$val->username;
            $data[$key]['uName']         = (string)$val->name;
            $data[$key]['avatar']        = (string)$val->avatar;
            $data[$key]['state']         = (int)$val->is_status;
            $data[$key]['rememberToken'] = (string)$val->remember_token;
            $data[$key]['createdTime']   = (string)$val->created_at;
            $data[$key]['agentId']       = $val->agent_id;
            $data[$key]['updatedTime']   = (string)$val->updated_at;
            $data[$key]['clientId']      = $val->client_id;
            $data[$key]['loginIp']       = (string)$val->login_ip;
            foreach ($val['roles'] as $v) {
                $data[$key]['roleId'] = (int)$v->id;
                $data[$key]['roleName'] = $v->name  ;
            }
        }
        self::setCount($count);
        return $this->responseJson($data);
    }

    //更新
    public function usersUpdate()
    {
        $put                   = self::requestParam(__FUNCTION__);
        Users::$where['id']    = $put['Id'];
        $editData              = [];
        if (isset($put['uName'])) {
            $editData['name']      = $put['uName'];                      //账号名称
        }
        if (isset($put['state'])) {
            $editData['is_status'] = $put['state'];                      //状态
        }
        if (isset($put['loginIp'])) {
            $editData['login_ip']  = $put['loginIp'];                    //IP地址
        }
        if (isset($put['agentId'])) {
            $editData['agent_id']  = $put['agentId'];                    //代理线
        }

        //关联表需要修改的数据
        Users::$data     = $editData;
        if ($result = Users::editToData($put['Id'])) {
            $time = ['updatedTime' => date('Y-m-d H:i:s')];
            return $this->responseJson($this->translateInfo(Code::UPDATE_SUCCESS), $time);
        } else {
            return $this->responseJson(Code::FAIL_TO_UPDATE);
        }
    }


    /**
     * 管理员账号添加
     */
    public function usersAdd()
    {
        $post                      = self::requestParam(__FUNCTION__);
        $addData                   = [];
        $addData['username']       = $post['account'];             //用户名
        $addData['name']           = $post['uName'];               //账号名称
        $addData['password']       = bcrypt($post['password']);      //密码
        $addData['created_at']     = date('Y-m-d H:i:s');   //创建时间
        $addData['is_status']      = $post['state'];   //是否启用
        $addData['client_id']      = $post['clientId'];   //所属平台线
        $addData['agent_id']       = (string)$post['agentId'];   //所属代理线
        $addData['login_ip']       = $post['loginIp'];   //允许登录的IP
        $addData['remember_token'] = Str::uuid() . time();

        Users::$data     = $addData;
        Users::$UserRole = $post['roleId'];
        Users::$Craving = ['roles'];

        if ($res = Users::addToData()) {
            Users::_destroy();
            //字段伪装
            $data                = [];
            $data['clientId']    = $res->client_id;
            $data['account']     = $res->username;
            $data['state']       = $res->is_status;
            $data['uName']       = $res->name;
            $data['Id']          = $res->id;
            $data['agentId']     = $res->agent_id;
            $data['loginIp']     = $res->login_ip;
            $data['roleId']      = (int)$post['roleId'];
            $data['roleName']    = $res['roles'][0]['name'];
            $data['createdTime'] = (string)$res->created_at;
            $data['updatedTime'] = (string)$res->updated_at;
            return $this->responseJson($this->translateInfo(Code::ADD_SUCCESS), $data);
        } else {
            return $this->responseJson(Code::FAIL_TO_ADD);
        }
    }

    /**
     * 账号修改密码(管理员修改他人)
     */
    public function usersPwdUpdate()
    {
        $put   = self::requestParam(__FUNCTION__);
        $users = Users::getOne($put['Id']);
        Users::_destroy();
        //新密码
        $newPassword = $put['password'];
        //新密码与旧密码相同
        if (Hash::check($newPassword, $users->password)) {
            return $this->responseJson(Code::PASSWORD_IS_SAME);
        }
        //要修改的数据
        $editData             = [];
        $editData['password'] = bcrypt($newPassword);
        Users::$data          = $editData;
        Users::$where['id']   = $put['Id'];
        $result               = Users::editToData();
        Users::_destroy();
        if ($result != 0) {
            return $this->responseJson(Code::CHANGE_OTHER_SUCCESS);
        } else {
            return $this->responseJson(Code::FAIL_TO_UPDATE);
        }

    }

    /**
     * 账号修改密码(自己修改自己)
     */
    public function editOwnPwd()
    {
        $put   = self::requestParam(__FUNCTION__);
        $users = Users::getOne(self::$adminUser->id);
        Users::_destroy();
        //旧密码
        $oldPassword = $put['oldPassword'];
        //新密码
        $newPassword = $put['password'];
        //旧密码错误
        if (!Hash::check($oldPassword, $users->password)) {
            return $this->responseJson(Code::PASSWORD_IS_ERROR);
        }
        //新密码与旧密码相同
        if (Hash::check($newPassword, $users->password)) {
            return $this->responseJson(Code::PASSWORD_IS_SAME);
        }
        //要修改的数据
        $editData             = [];
        $editData['password'] = bcrypt($newPassword);
        Users::$data          = $editData;
        Users::$where['id']   = self::$adminUser->id;
        $result               = Users::editToData();
        Users::_destroy();
        if ($result != 0) {
            return $this->responseJson(Code::CHANGE_PASS_SUCCESS);
        } else {
            return $this->responseJson(Code::FAIL_TO_UPDATE);
        }

    }

    /**
     * 获取角色选择列表
     */
    public function getRoleList()
    {
        //线路管理员
        if (self::$adminUser->client_id !== 0 && self::$adminUser->agent_id === '0') {

            Role::$where[] = ['view_client', '=', 2];
            Role::$where[] = ['view_agent', '=', 2];
        }
        Role::$where[] = ['status', '=', 1];

        $roleList = Role::getList();
        Role::_destroy();
        //伪装
        $data = [];
        foreach ($roleList as $key => $value) {
            $data[$key]['Id']       = $value->id;
            $data[$key]['label']    = $value->name;
            $data[$key]['isClient'] = $value->view_client;
            $data[$key]['isAgent']  = $value->view_agent;
        }
        return $this->responseJson($data);

    }

    /**
     * 通过token获取用户信息
     */

    public function getUsers()
    {
        $post = self::requestParam(__FUNCTION__);
        $adminUser = self::$adminUser;
        $key       = 'adminSession' . md5("{$adminUser->id}-{$adminUser->username}");
        $userInfo = RedisConPool::get($key);
        if (empty($userInfo) || $userInfo == '[]') {
            return self::getInstance()->responseJson(Code::PLEASE_LOGIN_AGAIN);
        }
        if (md5("{$adminUser->id}-{$adminUser->username}") === $post['token']) {
            $userInfo = json_decode(RedisConPool::get($key),true);
            if (!empty($userInfo['roles'])) {
                $role = [
                    'Id' => $userInfo['roles'][0]['id'],
                    'name' => $userInfo['roles'][0]['name'],
                ];
            }

            $data                  = [];
            $data['token']         = $post['token'];
            $data['userName']      = $userInfo['username'];
            $data['nickName']      = $userInfo['name'];
            $data['menu']          = isset($userInfo['menus']) ? $userInfo['menus'] : null;
            $data['roles']         = isset($role) ? $role : null;
            $data['isView']        = array(
                'isClient' => $userInfo['view_client'],
                'isAgent'  => $userInfo['view_agent'],
            );
            $data['client'] = $userInfo['client_id'];
            $data['agent']  = $userInfo['agent_id'];
            return $this->responseJson(self::translateInfo(Code::LOGIN_SUCCESS), $data);
        } else {
            return self::getInstance()->responseJson(Code::PLEASE_LOGIN_AGAIN);
        }




    }
}