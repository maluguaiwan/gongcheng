<?php
namespace app\home\controller;

use app\common\controller\IndexBase;
use app\common\model\Product;
use app\common\model\Category;
use app\common\model\ProductAssess;
use app\common\model\Member;
use app\common\model\ShoppingCar;
use app\common\model\SearchLog as SearchLogModel;
use app\common\model\Order;
use app\common\model\OrderProducts;
use app\common\model\ProductOrderView;

class Medicine extends IndexBase
{
	/**
	 * 药品列表
	 * @return mixed|string
	 */
	public function index(){
		$categoryId = $this->request->param('id',0);
		$search = $this->request->param('search','');
		/*
		 * 如果有药品搜索，插入最近搜索
		 */
		if ($search){
		    $user_id=$this->member['id'];
		    $model_sl=new SearchLogModel();
		    $check_search=$model_sl->where('user_id="'.$user_id.'" and name="'.$search.'"')->count();
		    if ($check_search<1){
                $data_sl=array(
                    'user_id'=>$user_id,
                    'name'=>$search,
                    'type'=>1,
                    'num'=>1,
                    'create_time'=>time(),
                );
                $model_sl->save($data_sl);
            }else{
		        $model_sl->where('user_id="'.$user_id.'" and name="'.$search.'"')->setInc('num');
            }
        }
		$categoryTitle = '所有药品';
		if($categoryId){
			$catrgory = Category::get($categoryId);
			$categoryTitle = $catrgory->name;
		}
		$this->assign('category_id',isset($catrgory->id)?$catrgory->id:0);
		$this->assign('ctitle',$categoryTitle);
		$this->assign('search',$search);
		return $this->fetch();
	}
	
	/**
	 * 药品数据
	 * @return unknown[]
	 */
	public function data(){
		$categoryId = $this->request->param('category_id',0);
		$page = $this->request->param('page',1);
		$sort_type = $this->request->param('sort',0);
		$search = $this->request->param('search','');
		$where = ['is_delete'=>0,'status'=>1];
		if($categoryId){
			$where['cat_id'] = $categoryId;
		}
		$sort = "sort ASC";
		if($sort_type == 1){
			$sort = "sale_count DESC";
		}elseif ($sort_type == 2){
			$sort = "price DESC";
		}
		
		if($search){
			$datas = Product::where($where)->whereLike('title','%'.trim($search).'%')->order($sort)->page($page,5)->select();
		} else {
			$datas = Product::where($where)->order($sort)->page($page,5)->select();
		}
		if(!empty($datas)){
			foreach ($datas as &$data){
				$data['link'] = url('medicine_detail',['id'=>$data['id']]);
			}
		}
		return ['data'=>$datas];
	}
	
	/**
	 * 药品详情
	 * @return mixed|string
	 */
	public function detail(){
		$id = $this->request->param('id');
		if(!$id){
			$this->error('参数错误');
		}
		$product = Product::get($id);
		if(!$product){
			// 404
			$this->error('药品不存在');
		}
		if(!empty($product->pics)){
			$product['pics'] = json_decode($product->pics,true);
		}
		$this->assign('product',$product);
		
		// 获取分类
		$category = Category::get($product->cat_id);
		
		$this->assign('category',$category);
		return $this->fetch();
	}
	
	/**
	 * 评论数据 ajax
	 * @return string[]|array[]
	 */
	public function assess(){
		$productId = $this->request->param('product_id',0);
		if(!$productId){
			return ['data'=>''];
		}
		$page = $this->request->param('page',1);
		$assess = ProductAssess::where(['product_id'=>$productId])->page($page,5)->order('create_time','DESC')->select();
		if(!empty($assess)){
			foreach ($assess as &$v){
				if($v['member_id']){
					$member = Member::get($v['member_id']);
					$v['headerimgurl'] = $member->headimgurl?:'/static/home/img/default_head.png';
					$v['name'] = $member->name ? : $member->nickname;
				}else{
					$v['headerimgurl'] = '/static/home/img/default_head.png';
					$v['name'] = '匿名会员';
					$v['content'] = '该会员暂时没有评论';
				}
			}
		}
		return ['data'=>$assess?:[]];
	}
	
	/**
	 * 加入购物车
	 */
	public function addCar(){
		if($this->request->isAjax()){
			$id = $this->request->post('id');
			if(!$id){
				return json(['errcode'=>1,'errmsg'=>'参数有误']);
			}
			$type = $this->isDoctor ? 'doctor' : 'member';
			$data = [];
			if($type == 'doctor'){
				$member_id = $this->request->post('member_id');
				if(!$member_id){
					return json(['errcode'=>1,'errmsg'=>'参数错误']);
				}
				$find = ShoppingCar::where(['product_id'=>$id,'member_id'=>$member_id,'status'=>0])->find();
				if($find){
					$count = $find->count+1;
					$status = ShoppingCar::update(['count'=>$count],['id'=>$find->id]);
				}else{
					$data = [
							'product_id' => $id,
							'member_id'  => $member_id,
							'doctor_id'  => $this->member['id'],
							'count'      => 1,
							'create_time' =>time()
					];
					$status = ShoppingCar::insert($data);
				}
				
			}else{
				$member_id = $this->member['id'];
				$find = ShoppingCar::where(['product_id'=>$id,'member_id'=>$member_id,'status'=>0])->find();
				if($find){
					$count = $find->count+1;
					$status = ShoppingCar::update(['count'=>$count],['id'=>$find->id]);
				}else{
					$data = [
							'product_id' => $id,
							'member_id'  => $member_id,
							'doctor_id'  => 0,
							'count'      => 1,
							'create_time' =>time()
					];
					$status = ShoppingCar::insert($data);
				}
			}
			if($status !== false){
				return json(['errcode'=>0,'errmsg'=>'ok']);
			}
			return json(['errcode'=>1,'errmsg'=>'加入清单失败']);
		}
	}
	
	/**
	 * 加入购物车
	 */
	public function addOrder(){
		$id = $this->request->param('id');
		if(!$id){
			$this->error('参数有误');
		}
		$type = $this->isDoctor ? 'doctor' : 'member';
		$data = [];
		if($type == 'doctor'){
			$member_id = $this->request->param('member_id');
			if(!$member_id){
				$this->error('参数有误');
			}
			$data = [
					'product_id' => $id,
					'member_id'  => $member_id,
					'doctor_id'  => $this->member['id'],
					'create_time' =>time()
			];
		}else{
			$member_id = $this->member['id'];
			$data = [
					'product_id' => $id,
					'member_id'  => $member_id,
					'doctor_id'  => 0,
					'count'      => 1,
					'update_time' => time(),
					'create_time' => time()
			];
		}
		$id = ShoppingCar::insertGetId($data);
		if($id){
			$url = url('order_create').'?ids='.$id;
			$this->redirect($url);
		}
		$this->error('加入清单失败');
	}
	
	/**
	 * 评价
	 */
	public function comment(){
		if($this->request->isPost()){
			$id = $this->request->param('id');
			$product = Product::get($id);
			if(!$product){
				$this->error('商品不存在');
			}
			$product = ProductOrderView::where(['product_id'=>$id,'member_id'=>$this->member['id']])->find();
			if(!$product){
				$this->error('该商品没有被购买不存在');
			}
			$order = Order::where(['id'=>$product->order_id,'member_id'=>$this->member['id']])->find();
			if(!$order){
				$this->error('您没有购买此商品不能评价');
			}
			if($order->status != 3){
				$this->error('该商品未完成或已评价');
			}
			$comment = ProductAssess::where(['member_id'=>$this->member['id'],'product_id'=>$id])->find();
			if($comment){
				$this->error('您已评价过商品,无需再次评价');
			}
			$insert = [
					'product_id' => $id,
					'member_id'  => $this->member['id'],
					'score'      => $this->request->param('score',5),
					'content'    => $this->request->param('content',''),
					'create_time' => time()
			];
			$id = ProductAssess::create($insert);
			if($id){
				$update = ['status'=>4,'update_time'=>time()];
				Order::where(['id'=>$order->id])->update($update);
				$this->success('评价成功',url('order_index'));
			}else{
				$this->error('评价失败，请联系管理员');
			}
			
			
			
		}else{
			$id = $this->request->param('id');
			if(!$id){
				$this->error('参数有误');
			}
			$product = Product::get($id);
			if(!$product){
			    $this->error('订单不存在');
			}
			$this->assign('product',$product);
			return $this->fetch();
		}
	}
}
