<?php

namespace App\Models\Auth;

use App\Extensions\Code;
use App\Models\ApiModel;

class Menu extends ApiModel
{
    protected $table = 'admin_menu';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    public static $menuRoles = [];
    public static $menuRoleIds = [];
    public static $addRoles = [];


    /**
     * 菜单-角色关联
     */
    public function roles()
    {
        return $this->belongsToMany(Menu::class, 'admin_role_menu', 'menu_id', 'role_id');
    }

    /**
     * 重写更新方法
     */
    public static function editToData(int $id = 0)
    {
        $client = parent::getOne($id);

        if (!empty(self::$menuRoles)) {
            $client->roles()->sync(self::$menuRoles, $id);
        }

        return parent::editToData();
    }
    /**
     * 删除
     */

    public static function delToData(int $id = 0)
    {
        $client = parent::getOne($id);
//        $client->delete($id);
        $client->roles()->detach();
        return parent::delToData($id);
    }


    /**
     * 重写添加方法
     */
    public static function addToData(int $id = 0)
    {
        $client = parent::addToData();
        $client->roles()->attach(self::$addRoles);
        return $client;
    }

}