<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Extensions\Code;
use App\Http\Controllers\AdminController;
use App\Models\Auth\Menu;


class MenusController extends AdminController
{
    /**
     * 菜单管理列表
     */
    public function menusSelect()
    {
        $count         = Menu::getListCount();
        Menu::$orderBy = ['id'];
        $menus         = Menu::getList();
        Menu::_destroy();
        $data = [];
        foreach ($menus as $v) {
            if ($v->parent_id == 0) {
                $data[] = array(
                    'parentId' => $v->parent_id,
                    'Id'       => $v->id,
                    'label'    => $v->title,
                    'icon'     => $v->icon,
                    'path'     => $v->slug,
                    'sort'     => $v->order,
                );
            }
        }
        foreach ($menus as $val) {
            if ($val->parent_id != 0) {
                foreach ($data as $j => $p) {
                    if ($val->parent_id == $p['Id']) {
                        $children['parentId']   = $val->parent_id;
                        $children['Id']         = $val->id;
                        $children['label']      = $val->title;
                        $children['icon']       = $val->icon;
                        $children['path']       = $val->slug;
                        $children['sort']       = $val->order;
                        $data[$j]['children'][] = $children;
                    }
                }
            }
        }
        self::setCount($count);
        return $this->responseJson($data);
    }

    /**
     * 菜单管理更新
     */
    public function menusUpdate()
    {
        $put                   = self::requestParam(__FUNCTION__);
        Menu::$where['id']     = $put['Id'];
        $editData              = [];
        $editData['parent_id'] = $put['parentId'];                          //父级ID
        $editData['title']     = $put['label'];                         //菜单名称
        $editData['icon']      = $put['icon'];                          //图标
        $editData['slug']       = $put['path'];                               //路径

        Menu::$data = $editData;
        $result     = Menu::editToData($put['Id']);
        $time = ['updatedTime' => date('Y-m-d H:i:s')]; // 更新时间
        Menu::_destroy();
        if ($result == 1) {
            return $this->responseJson($this->translateInfo(Code::UPDATE_SUCCESS), $time);
        } else {
            return $this->responseJson(Code::FAIL_TO_UPDATE);
        }

    }

    /**
     * 菜单管理删除
     */
    public function menusDel()
    {
        //获取删除的ID
        $del = self::requestParam(__FUNCTION__);
        //查询该菜单下是否有子集菜单
        Menu::$where['parent_id'] = $del['Id'];
        $obj                      = Menu::getList();
        Menu::_destroy();
        $ids = [];
        if (isset($obj[0]->id)) {
            //有子集，需要删除的ID装入数组
            foreach ($obj as $id) {
                array_push($ids, $id->id);
            }
            array_push($ids, (int)$del['Id']);
        } else {
            //没有子集、只删除当前菜单
            array_push($ids, (int)$del['Id']);
        }
        $result = '';
        foreach ($ids as $id) {
            $result = Menu::delToData($id);
        }
        if ($result) {
            return $this->responseJson(Code::DELETE_SUCCESS);
        } else {
            return $this->responseJson(Code::FAIL_TO_DEL);
        }

    }

    /**
     * 菜单管理添加
     */
    public function menusAdd()
    {
        $post                 = self::requestParam(__FUNCTION__);
        $addData              = [];
        $addData['parent_id'] = isset($post['parentId']) ? $post['parentId'] : 0;
        $addData['title']     = $post['label'];                       //菜单名
        $addData['icon']      = $post['icon'];                        //图标
        $addData['slug']      = $post['path'];                             //路径


        Menu::$data = $addData;
        $result     = Menu::addToData();
        Menu::_destroy();
        $order = 0;##暂时不用排序值、默认0
        if ($result->order) {
            $order = $result->order;
        }

        if ($result->id) {
            //返回数据伪装
            $data = array(
                [
                    'Id'    => $result->id,
                    'parentId' => $result->parent_id,
                    'label'    => $result->title,
                    'icon'     => $result->icon,
                    'path'     => $result->slug,
                    'sort'     => $order,
                ],
            );
            return $this->responseJson($this->translateInfo(Code::ADD_SUCCESS), $data);
        } else {
            return $this->responseJson(Code::FAIL_TO_ADD);
        }
    }
}