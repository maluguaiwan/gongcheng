<?php
namespace app\home\controller;

use app\common\controller\IndexBase;
use app\common\model\News as NewsModel;
use think\Request;

class News extends IndexBase
{
    /**
     * cate_id :1快讯 2在线帮助 3推广中心
     * 详情
     */
	public function detail(Request $request){
	    $id=$request->param('id');
	    if (empty($id)){
	        return $this->error('参数错误');
        }
	    $model=new NewsModel();
	    $info=$model->where('id="'.$id.'"')->find();
	    $this->assign('info',$info);
	    return $this->fetch();
	}
}
