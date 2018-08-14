<?php
namespace app\home\controller;

use app\common\controller\IndexBase;
use app\common\model\ShoppingCar;
use app\common\model\MemberAddress;
use app\common\model\Member;
use think\Db;
use org\weixin\WXPayTools;
use think\facade\Log;
use org\tools\SystemTool;
use think\Exception;
use app\common\model\Order as OrderModel;
use app\common\model\OrderProducts;
use app\common\model\MemberCoupon;
use app\common\model\Coupon;

class Order extends IndexBase
{
	/**
	 * 订单首页
	 */
	public function index(){
		$data = [];
		$status = $this->request->param('status','all');
		if($this->isDoctor){
			$member_id = $this->request->post('member_id');
		}else{
			$member_id = $this->member['id'];
		}
		if(!$member_id){
			$this->error('数据有误，刷新页面重试');
		}
		$where = ['member_id'=>$member_id];
		if($status != 'all'){
			if($status == 'wait'){
				$where['status'] = 0;
			}elseif ($status == 'ship'){
				$where['status'] = 1;
			}elseif ($status == 'receipt'){
				$where['status'] = 2;
			}elseif ($status == 'comment'){
				$where['status'] = 3;
			}
		}
		$this->assign('status',$status);
		if($member_id){
			$data = OrderModel::where($where)->order('create_time','desc')->select();
			foreach ($data as &$v){
				$productList = OrderProducts::with('product')->where(['order_id'=>$v['id']])->select();
				if(!empty($productList)){
					foreach ($productList as &$product){
						$product['link'] = url('medicine_detail',['id'=>$product->product_id]);
						$product['left_price'] = ($product->product->originnal_price - $product->product->price) > 0 ? ($product->product->originnal_price - $product->product->price) : 0;
					}
					$v['products'] = $productList;
					$v['order_detail'] = url('order_detail',['order_id'=>$v['id']]);
					$v['comment_url'] = url('order_detail',['order_id'=>$v['id']]);;
				}
			}
			$this->assign('data',$data);
			return $this->fetch();
		}
		$this->error('数据有误，退出重进');
		
	}
	
	public function detail(){
		$id = $this->request->param('order_id');
		if(!$id){
			$this->error('参数错误');
		}
		$order = OrderModel::where(['id'=>$id])->find();
		if(!$order){
			$this->error('订单不存在');
		}
		$data = OrderProducts::with('product')->where(['order_id'=>$id])->select();
		$data = $data->toArray();
		$total_price = 0;
		$left_price = 0;
		foreach ($data as &$v){
			$v['total_price'] = $v['count'] * $v['product']['price'];
			$v['product_left_price'] = $v['product']['originnal_price'] - $v['product']['price'];
			$v['left_price'] = $v['count'] * $v['product']['originnal_price'] - $v['total_price'];
			$total_price += $v['total_price'];
			$left_price += $v['left_price'];
		}
		$this->assign('data',$data);
		$this->assign('total_price',$total_price);
		$this->assign('left_price',$left_price);
		$address = MemberAddress::where(['member_id'=>$this->member['id']])->find();
		$this->assign('address',$address);
		$this->assign('order',$order);
		return $this->fetch();
	}


	
	/**
	 * 订单数据
	 */
	public function data(){
		$status = $this->request->param('status','all');
		$page = $this->request->param('page',1);
		
		$status_num = 0;
		$where = ['member_id'=>$this->member['id']];
		if($status != 'all'){
			if($status == 'wait'){
				$where['status'] = 0;
			}elseif ($status == 'ship'){
				$where['status'] = 1;
			}elseif ($status == 'receipt'){
				$where['status'] = 2;
			}elseif ($status == 'comment'){
				$where['status'] = 3;
			}
		}
		$datas = OrderModel::where($where)->order('create_time','DESC')->page($page,5)->select();
		return ['data'=>$datas];
	}
	
	/**
	 * 从购物车创建订单
	 */
	public function create(){
		$carIds = $this->request->param('ids','');
		if(!$carIds){
			$this->error('参数不正确');
		}
		$this->assign('ids',$carIds);
		$data = ShoppingCar::with('product')->where(['status'=>0])->whereIn('id',$carIds)->select();
		$data = $data->toArray();
		$total_price = 0;
		$left_price = 0;
		foreach ($data as &$v){
			$v['total_price'] = $v['count'] * $v['product']['price'];
			$v['product_left_price'] = $v['product']['originnal_price'] - $v['product']['price'];
			$v['left_price'] = $v['count'] * $v['product']['originnal_price'] - $v['total_price'];
			$total_price += $v['total_price'];
			$left_price += $v['left_price'];
		}
		
		
		$address = MemberAddress::where(['member_id'=>$this->member['id']])->find();
		$this->assign('address',$address);
		
		//查询是否使用优惠券
		$coupon_id = 0;
		$couponTitle = "没有优惠券";
		if(session('?member_coupon_id')){
			$memberCoupon = MemberCoupon::with(['coupon'])->find(session('member_coupon_id'));
			if($memberCoupon){
				$total_price = ($total_price - $memberCoupon->coupon->amount) > 0 ? ($total_price - $memberCoupon->coupon->amount):0;
				$left_price = $left_price + $memberCoupon->coupon->amount;
				$couponTitle = $memberCoupon->coupon->title;
				$coupon_id = $memberCoupon->coupon_id;
			}
		}
		
		$this->assign('data',$data);
		$this->assign('total_price',$total_price);
		$this->assign('left_price',$left_price);
		$this->assign('coupon_title',$couponTitle);
		$this->assign('coupon_id',$coupon_id);
		return $this->fetch();
		
	}
	
	/**
	 * 修改地址信息
	 */
	public function address(){
		$ids = $this->request->param('ids');
		if(!$ids){
			$this->error('参数不正确');
		}
		$this->assign('url',url('order_create').'?ids='.$ids);
		
		$member = Member::where(['openid'=>$this->openid])->find();
		$this->assign('member',$member);
		$info = MemberAddress::where(['member_id'=>$member->id])->find();
		$this->assign('info',$info);
		
		return $this->fetch();
	}
	
	/**
	 * 备注信息
	 */
	public function remark(){
		if($this->request->isPost()){
			$remark = $this->request->post('remark');
			if($remark){
				session('remark',$remark);
			}
			return json(['errcode'=>0,'errmsg'=>'ok']);
		}else{
			$ids = $this->request->param('ids');
			if(!$ids){
				$this->error('参数不正确');
			}
			if(session("?remark")){
				$this->assign('remark',session('remark'));
			}
			$this->assign('url',url('order_create').'?ids='.$ids);
			return $this->fetch();
		}
	}
	
	/**
	 * 选择优惠券
	 */
	public function coupon(){
		if($this->request->isPost()){
			$id = $this->request->post('id');
			if($id){
				session('member_coupon_id',$id);
			}
			return json(['errcode'=>0,'errmsg'=>'ok']);
		}else{
			$ids = $this->request->param('ids');
			if(!$ids){
				$this->error('参数不正确');
			}
			$this->assign('url',url('order_create').'?ids='.$ids);
			
			$coupons = MemberCoupon::with('coupon')->where(['member_id'=>$this->member['id'],'status'=>1])->select();
            if ($coupons){
                foreach ($coupons as $key=>$val){
//                $use_time=date('Y-m-d H:i',strtotime($val['create_time'])+$val['coupon']['use_length']*86400);
                    $use_time=strtotime($val['create_time'])+$val['coupon']['use_length']*86400;
                    $check=time()-$val['coupon']['validity_time'];//判断创建的优惠券是否过期
                    if ($check>0){
                        $status=-1;
                    }else{
                        $check_2=time()-$use_time;//判断发送给我的的优惠券有效期内是否过期。
                        if ($check_2>0){
                            $status=-1;
                        }else{
                            $status=date('Y-m-d H:i',$use_time);
                        }
                    }
                    $coupons[$key]['coupon']['use_time_zh']=$status;
                }
            }
			$this->assign('coupons',$coupons);
			
			return $this->fetch();
		}
	}
	
	/**
	 * 创建订单保存
	 */
	public function save(){
		$ids = $this->request->post('ids');
		if(!$ids){
			return json(['errcode'=>1,'errmsg'=>'参数错误']);
		}
		$addressId = $this->request->post('address_id');
		if(!$addressId){
			return json(['errcode'=>1,'errmsg'=>'地址信息错误']);
		}
		$coupon_id = $this->request->param('coupon_id',0);
		
		$address = MemberAddress::get($addressId); // 地址 对象
		$data = ShoppingCar::with('product')->where('id','in',$ids)->select();
		$total_price = 0;
		foreach ($data as &$v){
			$v['total_price'] = $v->count * $v->product->price;
			$total_price += $v['total_price'];
		}
		if($coupon_id){
			$coupon = Coupon::get($coupon_id);
			if($coupon){
				$total_price = ($total_price - $coupon->amount) > 0 ? ($total_price - $coupon->amount) : 0;
			}
		}
		Db::startTrans();
		try {
			$order_no = WXPayTools::createOrderNo();
			$notify= url('wxpay_notity','',true,true);
			Log::write('order_notify_url:'.$notify);
			$result=WXPayTools::unifiedOrder($this->openid, $order_no, $this->member['name'].'的订单', 0.01*100,$notify,strtotime('+2 day'));
			if(!empty( $result['code']) && $result['code']==1){
				$insert = [
						'order_number'  => $order_no,
						'member_id'     => $this->member['id'],
						'total_amount'  => $total_price,
						'name'          => $address->name,
						'mobile'        => $address->mobile,
						'address'       => $address->address,
						'unit'          => $address->unit,
						'coupon'        => $coupon_id,
						'pay_type'      => 1,
						'pay_name'      => '微信支付',
						'note'          => session('remark')?:'',
						'status'        => 0,
						'update_time'   => time(),
						'create_time'   => time()
				];
				$order_id = Db::name('order')->insertGetId($insert);
				if($order_id === false){
					throw new \Exception('订单创建失败',400);
				}
				
				foreach ($data as &$v){
					$v['total_price'] = $v->count * $v->product->price;
					$product_id = $v->product->id;
					Db::name('product')->where(['id'=>$product_id])->setInc('sale_count');
					$status = Db::name('product')->where(['id'=>$product_id])->setDec('count');
					if($status === false){
						throw new \Exception('药品不足',400);
					}
					$orderProduct = [
							'order_id'   => $order_id,
							'product_id' => $product_id,
							'price'      => $v->product->price,
							'count'      => $v['count'],
							'create_time' => time()
					];
					$op = Db::name('order_products')->insert($orderProduct);
					if($op === false){
						throw new \Exception('订单详细信息创建失败',400);
					}
					$update = Db::name("shopping_car")->where(['id'=>$v['id']])->update(['status'=>1]);
					if($update === false){
						throw new \Exception('订更新购物车失败',400);
					}
					if($coupon_id){
						$row = Db::name('member_coupon')->where(['member_id'=>$this->member['id'],'coupon_id'=>$coupon_id])->update(['status'=>2]);
						if($row === false){
							throw new \Exception('优惠券使用失败',400);
						}
					}
				}
				Db::commit();
				$paydata = weixin()->wxpay($result['result']['prepay_id'],SystemTool::getWeixinPayInfo('partnerKey'));
				if($paydata){
					
				}
				return json(['errcode'=>0,'paydata'=>$paydata,'order_id'=>$order_id]);
			}
			throw new \Exception('未知错误');
		}catch (\Exception $e){
			Db::rollback();
			return json(['errcode'=>1,'errmsg'=>$e->getMessage()?$e->getMessage():'创建订单失败']);
		}
		
	}
	
	/**
	 * 订单更新状态
	 */
	public function orderQuery(){
	    $order_id = $this->request->param('order_id');
	    if(!$order_id){
	        return json(['errcode'=>1]);
	    }
	    db('order')->where(['id'=>$order_id])->update(['status'=>1,'pay_status'=>3,'update_time'=>time()]);
	    return json(['errcode'=>0]);
	}
	
	/**
	 * 订单重新支付
	 */
	public function repay(){
		$orderId = $this->request->param('order_id');
		if(!$orderId){
			return json(['errcode'=>1,'errmsg'=>'参数错误']);
		}
		$order = OrderModel::get($orderId);
		if(!$order){
			return json(['errcode'=>1,'errmsg'=>'订单不存在']);
		}
		
		if($order->status > 0){
			return json(['errcode'=>1,'errmsg'=>'订单状态不符，刷新后重试']);
		}
		
		$notify= url('wxpay_notity','',true,true);
		// 上线需要修改此处代码
		$result=WXPayTools::unifiedOrder($this->openid, $order->order_number, $this->member['name'].'的订单', 0.01*100,$notify,strtotime('+2 day'));
		
		if(!empty( $result['code']) && $result['code']==1){
			$update = OrderModel::where(['id'=>$orderId])->update(['pay_status'=>1]);
			$paydata = weixin()->wxpay($result['result']['prepay_id'],SystemTool::getWeixinPayInfo('partnerKey'));
			return json(['errcode'=>0,'paydata'=>$paydata,'order_id'=>$orderId]);
		}
		return json(['errcode'=>1,'errmsg'=>'支付失败']);
	}
	
	public function update(){
		$orderId = $this->request->param('order_id');
		if(!$orderId){
			return json(['errcode'=>1,'errmsg'=>'参数错误']);
		}
		$order = OrderModel::get($orderId);
		if(!$order){
			return json(['errcode'=>1,'errmsg'=>'订单不存在']);
		}
		
		if($order->status != 2){
			return json(['errcode'=>1,'errmsg'=>'订单状态不符，刷新后重试']);
		}
		
		$status = OrderModel::where(['id'=>$orderId])->update(['status'=>3]);
		if($status !== false){
			return json(['errcode'=>0,'errmsg'=>'ok']);
		}
		return json(['errcode'=>1,'errmsg'=>'更新失败,刷新后重试']);
	}
}