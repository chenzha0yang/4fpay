<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Extensions\Code;
use App\Http\Controllers\AdminController;
use App\Models\Auth\Menu;
use App\Models\Auth\Permissions;
use App\Models\Auth\Role;

class RolesController extends AdminController
{
    /**
     * 角色管理列表
     */
    public function rolesSelect()
    {
        $get = self::requestParam(__FUNCTION__);
        Role::$limit = $this->getPageOffset(self::limitParam());
        //查询条件
        if (!empty($get['Id'])) {
            Role::$where['id'] = $get['Id'];
        }
        //按平台、代理线路查询
        if (self::$adminUser->name !== 'administrator') {
            //不是超管、不能查看角色
            return self::responseJson(Code::NO_PERMISSION);
        }
        Role::$Craving = ['permissions','menu'];
        $count         = Role::getListCount();
        $lists         = Role::getList()->toArray();
        Role::_destroy();
        $data          = [];
        foreach ($lists as $key => $val) {
            $data[$key]['Id']          = (int)$val['id'];
            $data[$key]['uName']       = (string)$val['name'];
            $data[$key]['slug']        = (string)$val['slug'];
            $data[$key]['state']       = (int)$val['status'];
            $data[$key]['createdTime'] = (string)$val['created_at'];
            $data[$key]['updatedTime'] = (string)$val['updated_at'];
            $data[$key]['isClient']    = (int)$val['view_client'];      //是否展示平台线路
            $data[$key]['isAgent']     = (int)$val['view_agent'];       //是否展示代理线路
            foreach ($val['permissions'] as $permission) {
                $data[$key]['permissionIds'][] = $permission['id'];
            }
            foreach ($val['menu'] as $menuVal) {
                $data[$key]['menuIds'][] = $menuVal['id'];
            }
        }
        self::setCount($count);
        return $this->responseJson($data);
    }

    /**
     * 角色管理更新
     */
    public function rolesUpdate()
    {
        $put               = self::requestParam(__FUNCTION__);
        Role::$where['id'] = $put['Id'];
        $editData          = [];
        if (isset($put['slug'])) {
            $editData['slug'] = $put['slug'];
        }
        if (isset($put['uName'])) {
            $editData['name'] = $put['uName'];
        }
        if (isset($put['state'])) {
            $editData['status'] = $put['state'];
        }
        if (isset($put['isClient'])) {
            $editData['view_client'] = $put['isClient'];
        }
        if (isset($put['isAgent'])) {
            $editData['view_agent'] = $put['isAgent'];
        }
        if (isset($put['permissionIds'])) {
            $permissionIds           = $put['permissionIds'];
            Role::$rolePermissionIds = $permissionIds;
        }
        if (isset($put['menuIds'])) {
            $menuIds         = $put['menuIds'];
            Role::$roleMenus = $menuIds;
        }
        //关联表需要修改的数据
        Role::$data = $editData;
        $result     = Role::editToData($put['Id']);
        $time       = ['updatedTime' => date('Y-m-d H:i:s')];
        Role::_destroy();
        if ($result == 1) {
            return $this->responseJson($this->translateInfo(Code::UPDATE_SUCCESS), $time);
        } else {
            return $this->responseJson(Code::FAIL_TO_UPDATE);
        }
    }


    /**
     * 角色管理添加
     */
    public function rolesAdd()
    {
        $post                   = self::requestParam(__FUNCTION__);
        $addData                = [];
        $addData['name']        = $post['uName'];                        //角色名
        $addData['slug']        = $post['slug'];                         //标识
        $addData['status']      = $post['state'];                        //标识
        $addData['view_client'] = $post['isClient'];                     //是否展示平台线路
        $addData['view_agent']  = $post['isAgent'];                     //是否展示代理线路
        $permissionIds          = $post['permissionIds'];   //权限
        $menuIds                = $post['menuIds'];   //权限

        Role::$data              = $addData;
        Role::$rolePermissionIds = $permissionIds;
        Role::$roleMenus         = $menuIds;


        if ($result = Role::addToData()) {
            $data = array(
                [
                    'Id'            => $result->id,
                    'state'         => $result->status,
                    'uName'         => $result->name,
                    'slug'          => $result->slug,
                    'permissionIds' => $permissionIds,
                    'menuIds'       => $menuIds,
                    'createdTime'   => (string)$result->created_at,
                    'updatedTime'   => (string)$result->updated_at,
                    'isView'        => array(
                        'isClient' => $result->view_client,
                        'isAgent'  => $result->view_agent,
                    ),
                ]
            );
            return $this->responseJson($this->translateInfo(Code::ADD_SUCCESS), $data);
        } else {
            return $this->responseJson(Code::FAIL_TO_ADD);
        }
    }
}