<?php
namespace app\home\controller;

use app\common\model\Category AS cate;
use app\common\controller\IndexBase;
use app\common\model\SearchKeywords as SearchKeywordsModel;
use app\common\model\SearchLog as SearchLogModel;

class Category extends IndexBase
{
	/**
	 * 医生分类页面
	 */
	public function doctor(){
		$category = cate::where(['status'=>1,'type'=>2,'parent_id'=>0])->order('sort','ASC')->select();
		if(!empty($category)){
			foreach ($category as $k => $c){
				$sub = cate::where(['status'=>1,'type'=>2,'parent_id'=>$c['id']])->order('sort','ASC')->select();
				$category[$k]['sub'] = $sub;
			}
		}
		$this->assign('category',$category);
		return $this->fetch();
	}
	
	/**
	 * 药品分类
	 */
	public function medicine(){
		$category = cate::where(['status'=>1,'type'=>1,'parent_id'=>0])->order('sort','ASC')->select();
		
		if(!empty($category)){
			foreach ($category as $k => $c){
				$sub = cate::where(['status'=>1,'type'=>1,'parent_id'=>$c['id']])->order('sort','ASC')->select();
				$category[$k]['sub'] = $sub;
			}
		}
		$this->assign('category',$category);
        /*
         * 热门搜索hk
         */
        $model_sk=new SearchKeywordsModel();
        $sk_list=$model_sk->order('sort ASC')->limit(10)->select();
        $this->assign('sk_list',$sk_list);
        /*
         * 搜索历史hk
         */
        $model_sl=new SearchLogModel();
        $sl_list=$model_sl->order('create_time DESC')->limit(10)->select();
        $this->assign('sl_list',$sl_list);
		return $this->fetch();
	}
}
