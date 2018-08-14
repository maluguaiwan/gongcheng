<?php
namespace app\common\model;


class AuthRule extends ModelBase
{
    /** 拼接菜单节点列表
     * @param $menu
     * @param int $id
     * @param int $level
     * @return array
     */
    public function menuList($menu,$id=0,$level=0){
        static $menus = array();
        foreach ($menu as $value) {
            if ($value['pid']==$id) {
                $value['level'] = $level+1;
                if($level == 0)
                {
                    $value['str'] = str_repeat('<i class="fa fa-angle-double-right"></i> ',$value['level']);
                }
                elseif($level == 2)
                {
                    $value['str'] = '&emsp;&emsp;&emsp;&emsp;'.'└ ';
                }
                else
                {
                    $value['str'] = '&emsp;&emsp;'.'└ ';
                }
                $menus[] = $value;
                $this->menulist($menu,$value['id'],$value['level']);
            }
        }
        return $menus;
    }
}