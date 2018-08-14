<?php
namespace app\admin\controller;

use think\Controller;
use think\facade\Request;



class Error extends Controller
{
    /**
     * 路由访问空方法跳转
     */
    public function index()
    {
        $this->error('你访问了一个错误的地址：'.Request()->url().' 正在为你跳转。。。','admin/index/welcome');
    }

    /**
     * 路由访问空控制器跳转
     */
    public function _empty()
    {
        $this->error('你访问了一个错误的地址：'.Request()->url().'<br/> 正在为你跳转。。。','admin/index/welcome');
    }


}