<?php
namespace app\common\controller;
use org\Auth;
use think\facade\Cookie;
use think\facade\Session;


/** 后台公用基础控制器
 * Class AdminBase
 * @package app\common\controller
 */
class AdminBase extends AppBase
{
    public $userSession;

    protected function initialize()
    {
        parent::initialize();
        if(Session::has('adminUser') == false) {
            $this->redirect('admin/login/index');
        }
        $this->userSession=Session::get('adminUser');
        $this->checkAuth('adminUser');
        $this->assign('skin_name', Cookie::get('skin_name'));
        //赋值当前菜单
        if(method_exists($this,'_infoModule')){
            $this->assign('infoModule',$this->_infoModule());
        }
    }

    /** 拼接菜单节点列表
     * @param $menu
     * @return array
     */
    protected function menuList($menu){
        $menus = array();
        //先找出顶级菜单

       $userInfo= $this->userSession;
        foreach ($menu as $k => $val) {
            if($val['pid'] == 0  and (new Auth())->check($val['name'],$userInfo['id'])) {
                $menus[$k] = $val;
            }
        }

        //通过顶级菜单找到下属的子菜单
        foreach ($menus as $k => $val) {
            foreach ($menu as $key => $value) {
//                if($value['pid'] == $val['id']) {
                if($value['pid'] == $val['id'] and (new Auth())->check($value['name'],$userInfo['id'])==true) {
                    $menus[$k]['list'][] = $value;
                }
            }
        }
        return $menus;
    }

}