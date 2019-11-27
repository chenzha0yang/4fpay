<?php

namespace App\Models\Auth;

use App\Models\ApiModel;
use App\Models\Auth\Menu;

class Role extends ApiModel
{
    protected $table = 'admin_roles';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    public static $rolePermissionIds = [];
    public static $roleMenus  = [];   // 角色关联菜单ID
    public static $roleUsers  = [];   // 角色关联用户数据

    /**
     * 用户-角色关联
     */
    public function users()
    {
        return $this->belongsToMany(Users::class, 'admin_role_users', 'role_id', 'user_id');
    }
    /**
     * 角色-权限关联
     */
    public function permissions()
    {
        return $this->belongsToMany(Permissions::class, 'admin_role_permissions', 'role_id', 'permission_id');
    }
    /**
     * 角色-菜单关联
     */
    public function menu()
    {
        return $this->belongsToMany(Menu::class, 'admin_role_menu', 'role_id', 'menu_id');
    }

    /**
     * 重写更新方法
     */
    public static function editToData(int $id = 0)
    {
        $client = parent::getOne($id);

        if (!empty(self::$rolePermissionIds)) {
            $client->permissions()->sync(self::$rolePermissionIds, $id);
        }
        if (!empty(self::$rolePermissionIds)) {
            $client->menu()->sync(self::$roleMenus, $id);
        }

        return parent::editToData();
    }
    /**
     * 重写添加方法
     */
    public static function addToData(int $id = 0)
    {
        $client = parent::addToData();
        if (!empty(self::$rolePermissionIds)) {
            $client->permissions()->attach(self::$rolePermissionIds);
        }
        if (!empty(self::$roleMenus)) {
            $client->menu()->attach(self::$roleMenus);
        }

        return $client;
    }
}