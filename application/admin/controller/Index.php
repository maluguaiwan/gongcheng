<?php
namespace app\admin\controller;
use app\common\controller\AdminBase;
use think\Db;
use think\facade\Session;

/** 后台首页控制器
 * Class Index
 * @package app\admin\controller
 */
class Index extends AdminBase
{
    /**
     * 后台初始化
     */
    protected function initialize()
    {
        parent::initialize();
    }

    /** 首页
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index(){

        $menu = Db::name('AuthRule')->where(['status'=>1])->order('orders asc')->select();
        //添加url
        foreach ($menu as $key => $value) {

            $menu[$key]['url'] =  url($value['name']);
        }
        $menus = $this->menuList($menu);
        $this->assign('menus',$menus);
        //环境
        $this->assign('dev',$this->getSysInfo());
        //检查是否锁屏状态
        $is_lock_screen = cookie('is_lock_screen') ? true : false;
        $this->assign('is_lock_screen',$is_lock_screen);
        //登录信息
        $this->assign('login',Session::get('adminUser'));
      return $this->fetch();

    }

    /** 系统环境检测
     * @return mixed
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     */
    public function getSysInfo(){
        //环境
        $this->assign($dev['php_version'] = PHP_VERSION);
        if (@ini_get('file_uploads')) {
            $dev['upload_max_filesize'] = ini_get('upload_max_filesize');
        } else {
            $dev['upload_max_filesize'] = '禁止上传';
        }
        $dev['php_os'] = PHP_OS;
        $softArr = explode('/',$_SERVER["SERVER_SOFTWARE"]) ;
        $dev['server_software'] = array_shift($softArr);
        $dev['server_name'] = gethostbyname($_SERVER['SERVER_NAME']);
        $rslt = db()->query('SELECT VERSION() AS `version`');
        $dev['mysql_version'] = $rslt[0]['version'];
        if (extension_loaded('curl')) {
            $dev['curl_extension'] = 'YES';
        } else {
            $dev['curl_extension'] = 'NO';
        }
        $dev['max_execution_time'] = ini_get('max_execution_time') . 'S';
        return $dev;
    }


    /** 默认欢迎页面
     * @return mixed
     */
    public function welcome(){
        return $this->fetch();
    }

}