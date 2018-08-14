<?php
namespace app\common\model;

class AuthGroup extends ModelBase
{
    protected $pk = 'id';
    /** 关联管理员用户表 多对多
     * @return mixed
     */
    public function adminUser()
    {
        return $this->belongsToMany('AdminUser','\app\common\model\AuthGroupAccess');
    }
    /** 主键id删除
     * @param int $id ID
     * @return bool 删除状态
     */
    public function del($id)
    {
        $map[] = [$this->pk,'eq',$id];
        return self::where($map)->delete();
    }
}