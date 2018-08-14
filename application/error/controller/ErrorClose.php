<?php
namespace app\error\controller;
use think\Controller;
use think\facade\Request;



class Error extends Controller
{

    /**  路由访问空控制器跳转
     * @return string
     */
    public function _empty()
    {
        if(request()->isGet()){
            $this->error('你访问了一个错误的地址：'.Request()->url().'<br/> 正在为你跳转。。。','home/index/index');
        }
        return json_error('你访问了一个错误的地址：'.Request()->url());

    }


}