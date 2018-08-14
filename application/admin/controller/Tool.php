<?php
namespace app\admin\controller;
use app\common\controller\AdminBase;
use app\common\model\AdminUser as AdminUserModel;
use think\facade\Cache;
use think\facade\Session;

/** 工具类控制器
 * Class Tool
 * @package app\admin\controller
 */
class Tool extends AdminBase
{

    /** 设置后台界面风格 颜色
     * @return string
     */
    public function set_skin()
    {
        if (!$this->request->isAjax()) {
            $this->error('不是一个正确的请求方式');
        }
        $data = $this->request->post();
        $skin = trim($data['skin']);
        cookie('skin_name', $skin, 2592000);
        return json_success('皮肤切换成功');
    }

    public function lock_screen()
    {
        if (!$this->request->isAjax()) {
            $this->error('不是一个正确的请求方式');
        }
        cookie('is_lock_screen', 1, 86400);
        $this->success('锁屏成功');
    }

    public function relieve_screen()
    {
        if (!$this->request->isAjax()) {
            return ajaxReturn(0,'不是一个正确的请求方式');
        }
        $user_id=Session::get('adminUser.id');
        $userModel=new AdminUserModel();
        $userInfo=$userModel->find($user_id);
        $data = $this->request->post();
        if (trim($data['pwd'])) {
            $lock_pwd = passwordMD5($data['pwd'],$userInfo['salt']);
            if ($lock_pwd == $userInfo['password']) {
                cookie('is_lock_screen', null);
                return ajaxReturn(200,'解屏成功');
            } else {
                return ajaxReturn(0,'密码输入不一致');
            }
            return ajaxReturn(200,'解屏成功');
        } else {
            return ajaxReturn(0,'请输入密码');
        }
    }
    public function clear_cache()
    {
        if (!$this->request->isAjax()) {
          $this->error('不是一个正确的请求方式');
        }
        Cache::clear();
        $this->success('缓存清除成功！');
    }
}