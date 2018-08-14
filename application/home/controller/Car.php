<?php
namespace app\home\controller;

use app\common\controller\IndexBase;
use app\common\model\ShoppingCar;

class Car extends IndexBase
{
	/**
	 * 购物车首页
	 */
	public function index(){
		$data = [];
		if($this->isDoctor){
			$member_id = $this->request->post('member_id');
		}else{
			$member_id = $this->member['id'];
		}
		if($member_id){
			$data = ShoppingCar::with('product')->where(['member_id'=>$member_id,'status'=>0])->select();
			foreach ($data as &$v){
				$v['total_price'] = $v->count * $v->product->price;
				$v->product->left_price = $v->product->originnal_price - $v->product->price;
				$v['left_price'] = $v->count * $v->product->originnal_price - $v['total_price'];
			}
		}
		$this->assign('data',$data);
		return $this->fetch();
	}
	
	/**
	 * 更新
	 */
	public function count(){
		$id = $this->request->param('id');
		$type = $this->request->param('type','plus');
		if(!$id || !$type){
			return json(['errcode'=>1,'errmsg'=>'参数错误']);
		}
		$car = ShoppingCar::get($id);
		if(!$car){
			return json(['errcode'=>1,'errmsg'=>'购物车不存在']);
		}
		if($type == 'plus'){
			$row = ShoppingCar::update(['count'=>$car->count+1,'update_time'=>time()],['id'=>$car->id]);
			if($row){
				return ['errcode'=>0];
			}
			return ['errcode'=>1];
		}else{
			$count = ($car->count - 1) > 0 ? ($car->count - 1) : 1;
			$row = ShoppingCar::update(['count'=>$count,'update_time'=>time()],['id'=>$car->id]);
			if($row){
				return ['errcode'=>0];
			}
			return ['errcode'=>1];
		}
	}
	
	/**
	 * 删除清单
	 */
	public function del(){
		$ids = $this->request->param('ids');
		if(!$ids){
			return json(['errcode'=>1,'errmsg'=>'参数有误']);
		}
		$row = ShoppingCar::whereIn('id',$ids)->delete();
		if($row !== false){
			return json(['errcode'=>0,'errmsg'=>'ok']);
		}
		return json(['errcode'=>1,'errmsg'=>'删除失败']);
	}
	
}