<?php

namespace App\Http\Controllers\Admin\Index;

use App\Extensions\Code;
use App\Extensions\Captcha;
use App\Http\Controllers\AdminController;
use App\Models\Auth\Menu;
use App\Models\Auth\Users;
use App\Models\Auth\Role;
use App\Extensions\RedisConPool;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Logs\LoginLogs;
use Illuminate\Support\Str;
use App\Models\Auth\Permissions;

class LoginController extends AdminController
{
    /**
     * 用户登录
     */
    public function login()
    {
        $post = self::requestParam(__FUNCTION__);

        //验证验证码
        if (Captcha::getInstance()->check($post['verification'], $post['vt'])) {
            return $this->responseJson(Code::CAPTCHA_FAIL);
        }

        Users::$where['username'] = $post['username'];
        Users::$Craving = ['roles'];
        $adminUser = Users::getOne();
        // 用户是否存在
        if (empty($adminUser)) {
            return $this->responseJson(Code::USERNAME_PASS_FAIL);
        }
        //账号是否停用
        if ($adminUser->is_status === 2) {
            return $this->responseJson(Code::USER_DISABLE);
        }

        // 验证密码
        if (!Hash::check($post['password'], $adminUser->password)) {
            return $this->responseJson(Code::USERNAME_PASS_FAIL);
        }

//        //验证IP是否在允许登陆范围内
//        if (empty($adminUser->login_ip)) {
//            return $this->responseJson(Code::NOT_ALLOWED_LOGIN_IP);
//        }
//        $ipList = explode(',', $adminUser->login_ip);
//        if (!in_array(self::getClientIp(0, true), $ipList, true)) {
//            return $this->responseJson(Code::NOT_ALLOWED_LOGIN_IP);
//        }

        $lastLoginTime = (string)$adminUser->last_login_at; //上次登录时间
        $lastLoginIp   = $adminUser->last_login_ip;           //上次登录IP
        //$adminUser->remember_token    = Str::uuid() . time();
        Users::$data['last_login_ip'] = self::getClientIp(0, true); // 本次登录ip
        Users::$data['last_login_at'] = date('Y-m-d H:i:s');// 本次登录时间
        Users::editToData();// 更新登录信息
        Users::_destroy();
        //角色ID
        if ($adminUser->name == 'administrator') {
            //系统账号直接返回所有菜单
            Role::$Craving  = null;
            Users::$Craving = null;
            $allMenus = Menu::getList();
            $ownRole['Id'] = 1;
            $ownRole['name'] = 'administrator';
            $adminUser->view_client = 1;
            $adminUser->view_agent  = 1;
            foreach ($allMenus as $adminKey => $adminMenu) {
                $ownMenus[$adminKey]['Id'] = $adminMenu->id;
                $ownMenus[$adminKey]['parentId'] = $adminMenu->parent_id;
                $ownMenus[$adminKey]['name'] = $adminMenu->slug;
                $ownMenus[$adminKey]['icon'] = $adminMenu->icon;
            }
            $ownMenus = self::listToTree($ownMenus,'Id','parentId','children');
        } else {
            $roleId                 = $adminUser['roles'][0]['id'];
            $adminUser->view_client = $adminUser['roles'][0]['view_client'];
            $adminUser->view_agent  = $adminUser['roles'][0]['view_agent'];
            Role::$where['id']      = $roleId;
            Role::$Craving = ['permissions','menu'];
            $roles = Role::getOne();
            Role::_destroy();
            //该用户拥有权限
            $ownPermissions = [];
            //用户拥有父级权限ID
            $ids = [];
            foreach ($roles['permissions'] as $key => $val) {
                if ($val['parent_id'] != 0) {
                    //如果是子权限直接加入
                    $ownPermissions[$key]['http_path']   = $val['http_path'];
                    $ownPermissions[$key]['http_method'] =  explode(',',$val['http_method']);
                } else {
                    //父级权限获取ID
                    $ids[] = $val['id'];
                }
            }
            $ownChildArr = [];
            //有父级权限才进行查询
            if (!empty($ids)) {
                Permissions::_destroy();
                //查询父级下所有权限
                Permissions::$Craving = null;
                Permissions::$field = ['http_method','http_path'];
                Permissions::$whereIn = ['parent_id' => $ids];
                $ownChild = Permissions::getList();
                foreach ($ownChild as $childKey => $child) {
                    $ownChildArr[$childKey]['http_path'] = $child['http_path'];
                    $ownChildArr[$childKey]['http_method'] = explode(',',$child['http_method']);
                }
            }
            //重新赋值用户拥有权限
            $ownPermissions = array_merge($ownChildArr,$ownPermissions);
            $adminUser->ownPermissions = $ownPermissions;

            $arr = [];
            //不是系统账号查询该用户拥有菜单
            $ownRole         = [];
            $ownRole['Id']   = $roles->id;
            $ownRole['name'] = $roles->name;

            foreach ($roles['menu'] as $k => $v) {

                if ($v->parent_id == 0) {
                    $pids[] = $v->id;
                } else {
                    $cids[] = $v->parent_id;
                }
                $arr[$k]['Id']       = $v->id;
                $arr[$k]['parentId'] = $v->parent_id;
                $arr[$k]['name']     = $v->slug;
                $arr[$k]['icon']     = $v->icon;
            }
            if (isset($cids)) $cids = array_values(array_unique($cids));

            //只有父级查子集

            Menu::$Craving = null;
            Menu::$field = ['id','parent_id','icon','slug'];
            if (isset($pids)) Menu::$whereIn = ['parent_id' => $pids];
            $menuChild = Menu::getList();

            $menuChildArr = [];

            foreach ($menuChild as $menuKey => $menuVal) {
                if (isset($menuVal->id)) {
                    $menuChildArr[$menuKey]['Id'] = $menuVal->id;
                    $menuChildArr[$menuKey]['parentId'] = $menuVal->parent_id;
                    $menuChildArr[$menuKey]['name'] = $menuVal->slug;
                    $menuChildArr[$menuKey]['icon'] = $menuVal->icon;
                }
            }

            //只有子集查父级
            Menu::$Craving = null;
            Menu::$field = ['id','parent_id','icon','slug'];
            if (isset($cids)) Menu::$whereIn = ['id' => $cids];
            $menuParent = Menu::getList();
            $menuParentArr = [];
            foreach ($menuParent as $menuPKey => $menuPVal) {
                $menuParentArr[$menuPKey]['Id'] = $menuPVal->id;
                $menuParentArr[$menuPKey]['parentId'] = $menuPVal->parent_id;
                $menuParentArr[$menuPKey]['name'] = $menuPVal->slug;
                $menuParentArr[$menuPKey]['icon'] = $menuPVal->icon;
            }
            $ownMenusAll = array_merge($menuChildArr,$menuParentArr,$arr);
            $ownMenus = self::unsetArray(self::listToTree(array_values(array_unique($ownMenusAll,SORT_REGULAR)), 'Id', 'parentId', 'children'));
            Role::_destroy();
        }
        $adminUser->menus = $ownMenus;
        self::$adminUser = $adminUser;
        $key             = md5("{$adminUser->id}-{$adminUser->username}");
        //记录登陆日志
        LoginLogs::LogInfo($adminUser, $key, $post['password']);

        RedisConPool::setEx(
            getRedis('adminSession', ['key' => $key]),
            config("app.loginTimer"),
            json_encode($adminUser)
        );

        $data                  = [];
        $data['token']         = $key;
        $data['userName']      = (string)$adminUser->username;
        $data['nickName']      = (string)$adminUser->name;
        $data['menu']          = $ownMenus;
        $data['roles']         = isset($ownRole) ? $ownRole : '';
        $data['isView']        = array(
            'isClient' => $adminUser->view_client,
            'isAgent'  => $adminUser->view_agent,
        );
        $data['client'] = $adminUser->client_id;
        $data['agent']  = $adminUser->agent_id;
        $data['lastLoginTime'] = $lastLoginTime; //上次登录时间
        $data['lastLoginIP']   = $lastLoginIp;           //上次登录IP
        return $this->responseJson(self::translateInfo(Code::LOGIN_SUCCESS), $data);
    }

    /**
     * 退出登陆
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        if (empty(request()->header("C-Token"))) {
            return $this->responseJson();
        }
        RedisConPool::del(getRedis('adminSession', [
            'key' => decrypt(request()->header("C-Token"))
        ]));
        self::$adminUser = null;

        return $this->responseJson();
    }

    function unsetArray($list, $arr = [], $child = [])
    {
        foreach ($list as $k => $value) {
            unset($value['Id'], $value['parentId']);
            $arr[$k] = $value;

            if (isset($value['children'])) {
                foreach ($value['children'] as $j => $v) {
                    unset($v['Id'], $v['parentId']);
                    $child[$j] = $v;
                }
                if (!empty($child)) {
                    $arr[$k]['children'] = $child;
                    unset($child);
                }
            }
        }
        return $arr;
    }

    function pr($var)
    {
        $template = PHP_SAPI !== 'cli' ? '<pre>%s</pre>' : "\n%s\n";
        printf($template, print_r($var, true));
    }


}
