<?php
namespace app\common\model;


use think\model\Pivot;

class AuthGroupAccess extends Pivot
{

    public function del($id)
    {
        $map[] = ['auth_group_id','eq',$id];
        return self::where($map)->delete();
    }
}