<?php

namespace App\Models\Auth;

use App\Models\ApiModel;
use Illuminate\Support\Facades\DB;

class Permissions extends ApiModel
{
    protected $table = 'admin_permissions';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    public static $preUsersIds = [];
    public static $preRolesIds = [];

    /**
     * 用户-权限关联
     */
    public function users()
    {
        return $this->belongsToMany(Users::class, 'admin_user_permissions', 'permission_id', 'user_id');
    }

    /**
     * 角色-权限关联
     */
    public function Roles()
    {
        return $this->belongsToMany(Role::class, 'admin_role_permissions', 'permission_id', 'role_id');
    }

    /**
     * 重写删除方法
     */
    public static function delToData(int $id = 0)
    {
        $client = parent::getOne($id);

        foreach ($client->Roles as $preRolesId) {
            $preRolesIds[] = $preRolesId->id;
        }
        foreach ($client->users as $preUsersId) {
            $preUsersIds[] = $preUsersId->id;
        }

        if (!empty($preRolesIds)) {
            $client->Roles()->detach($preRolesIds);
        }
        if (!empty($preUsersIds)) {
            $client->users()->detach($preUsersIds);
        }
        return parent::delToData($id);
    }

    public static function getName()
    {
        $re = DB::table('admin_permissions')->select(['name','id'])->get();
        return $re;
    }
}