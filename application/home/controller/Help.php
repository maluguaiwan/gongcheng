<?php
namespace app\home\controller;

use app\common\controller\IndexBase;
use app\common\model\News as NewsModel;

class Help extends IndexBase
{
    /**
     * cate_id :1快讯 2在线帮助 3推广中心
     */
	public function index(){
        $model=new NewsModel();
        $list=$model->where('cat_id=2 and is_visible=1')->order('sort ASC')->select();
        $this->assign('list',$list);
		return $this->fetch();
	}
}
