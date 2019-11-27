<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Extensions\Code;
use App\Http\Controllers\AdminController;
use App\Models\Auth\Permissions;
use App\Models\Auth\Menu;


class PermissionController extends AdminController
{
    /**
     * 权限管理列表
     */
    public function permissionSelect()
    {
        $count                = Permissions::getListCount();
        Permissions::$where[] = ['id', '<>', 1];
        $permissions          = Permissions::getList();
        Permissions::_destroy();
        $data = [];
        foreach ($permissions as $permission) {
            if ($permission->parent_id == 0) {
                $data[] = array(
                    'parentId' => $permission->parent_id,
                    'Id'       => $permission->id,
                    'label'    => $permission->name,
                    'slug'     => $permission->slug,
                    'method'   => !empty($permission->http_method) ? explode(',', $permission->http_method) : [],
                    'path'     => $permission->http_path,
                );
            }
        }
        foreach ($permissions as $val) {
            if ($val->parent_id != 0) {
                foreach ($data as $j => $p) {
                    if ($val->parent_id == $p['Id']) {
                        $children['parentId']   = $val->parent_id;
                        $children['Id']         = $val->id;
                        $children['label']      = $val->name;
                        $children['slug']       = $val->slug;
                        $children['method']     = !empty($val->http_method) ? explode(',', $val->http_method) : [];
                        $children['path']       = $val->http_path;
                        $data[$j]['children'][] = $children;
                    }
                }
            }
        }
        self::setCount($count);
        return $this->responseJson($data);
    }

    /**
     * 权限管理更新
     */
    public function permissionUpdate()
    {
        $put                      = self::requestParam(__FUNCTION__);
        Permissions::$where['id'] = $put['Id'];
        $res                      = json_decode(Permissions::getName(), true);
        foreach ($res as $value) {
            if ($value['id'] != $put['Id'] && $value['name'] == $put['label']) {
                return $this->responseJson(Code::HAVE_SAME_NAME);
            }
        }

        $editData                = [];
        $editData['name']        = $put['label'];                        //角色名
        $editData['parent_id']   = $put['parentId'];                        //角色名
        $editData['slug']        = $put['slug'];                         //标识
        $editData['http_method'] = strtoupper(implode(',', $put['method']));    //提交方式
        $editData['http_path']   = $put['path'];              //级别
        Permissions::$data       = $editData;
        $result                  = Permissions::editToData();
        $time = ['updatedTime' => date('Y-m-d H:i:s')]; // 更新时间
        Permissions::_destroy();
        if ($result == 1) {
            return $this->responseJson($this->translateInfo(Code::UPDATE_SUCCESS), $time);
        } else {
            return $this->responseJson(Code::FAIL_TO_UPDATE);
        }

    }

    /**
     * 权限管理删除
     */
    public function permissionDel()
    {
        $del                      = self::requestParam(__FUNCTION__);
        $id                       = $del['Id'];
        Permissions::$where['id'] = $id;
        $result                   = Permissions::delToData();
        Permissions::_destroy();
        if ($result == 1) {
            return $this->responseJson(Code::DELETE_SUCCESS);
        } else {
            return $this->responseJson(Code::FAIL_TO_DEL);
        }
    }

    /**
     * 权限管理添加
     */
    public function permissionAdd()
    {
        $post                   = self::requestParam(__FUNCTION__);
        $addData                = [];
        $addData['parent_id']   = isset($post['parentId']) ? $post['parentId'] : 0;
        $addData['name']        = $post['label'];                        //角色名
        $addData['slug']        = $post['slug'];                         //标识
        $addData['http_method'] = strtoupper(implode(',', $post['method']));    //提交方式
        $addData['http_path']   = $post['path'];              //级别
        Permissions::$data      = $addData;

        if ($result = Permissions::addToData()) {
            $data = array(
                [
                    'Id'       => $result->id,
                    'parentId' => $result->parent_id,
                    'label'    => $result->name,
                    'slug'     => $result->slug,
                    'method'   => explode(',', $result->http_method),
                    'path'     => $result->http_path,
                ]
            );
            return $this->responseJson($this->translateInfo(Code::ADD_SUCCESS), $data);
        } else {
            return $this->responseJson(Code::FAIL_TO_ADD);
        }
    }

    /**
     * 获取权限选择列表
     */
    public function getPermissions()
    {
        $permissions = Permissions::getList();
        Permissions::_destroy();
        $data = [];
        foreach ($permissions as $permission) {
            if ($permission->parent_id == 0 && $permission->slug != '*') {
                $data[] = array(
                    'id'    => $permission->id,
                    'label' => $permission->name,
                );
            } else {
                foreach ($data as $j => $p) {
                    if ($permission->parent_id == $p['id']) {
                        $children['id']         = $permission->id;
                        $children['label']      = $permission->name;
                        $data[$j]['children'][] = $children;
                    }
                }
            }
        }

        $arr[] = [
            'label'    => '全部',
            'id'       => '*',
            'children' => $data
        ];
        return $this->responseJson($arr);
    }

    /**
     * 获取菜单选择列表
     */
    public function getMenus()
    {
        $menus = Menu::getList();
        Menu::_destroy();
        $data = [];
        foreach ($menus as $menu) {
            if ($menu->parent_id == 0) {
                $data[] = array(
                    'id'    => $menu->id,
                    'label' => $menu->title,
                );
            } else {
                foreach ($data as $j => $p) {
                    if ($menu->parent_id == $p['id']) {
                        $children['id']         = $menu->id;
                        $children['label']      = $menu->title;
                        $data[$j]['children'][] = $children;
                    }
                }
            }
        }
        $arr[] = [
            'label'    => '全部',
            'id'       => '*',
            'children' => $data
        ];
        return $this->responseJson($arr);
    }

}