<?php
namespace app\common\model;

class AdminUser extends ModelBase
{
    /** 关联角色模型 多对多
     * @return mixed
     */
    public function roles()
    {
        return $this->belongsToMany('AuthGroup','\app\common\model\AuthGroupAccess');
    }

    /** 昵称读取器 （去盐值）
     * @param $value
     * @return mixed
     */
    public function getNicknameAttr($value){
        $return = explode('_',$value);
        return $return[0];
    }

    /** 批量更新用户-角色 中间表
     * @param $id
     * @param $roles
     * @return mixed
     */
    public function saveRolesByID($id,$roles){
        (new AuthGroupAccess())->where(['admin_user_id'=>$id])->field('auth_group_id')->delete();
        $user = $this::get($id);
        return $user->roles()->saveAll($roles);
    }
}