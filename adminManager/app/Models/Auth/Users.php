<?php

namespace App\Models\Auth;

use App\Models\ApiModel;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\This;

class Users extends ApiModel
{
    protected $table = 'admin_users';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    public static $UserRole = []; // 用户关联角色ID



    /**
     * 多对多
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'admin_role_users', 'user_id', 'role_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permissions::class, 'admin_user_permissions', 'user_id', 'permission_id');
    }

    ##重写更新方法
    public static function editToData(int $id = 0)
    {
        $client = parent::getOne($id);

        if (!empty(self::$UserRole)) {
            $client->roles()->sync(self::$UserRole, $id);
        }

        return parent::editToData();
    }

    ##重写增加
    public static function addToData(int $id = 0)
    {
        $client = parent::addToData();
        $client->roles()->attach(self::$UserRole);
        return $client;
    }
}