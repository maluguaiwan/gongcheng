<?php
namespace app\home\controller;


use app\common\controller\IndexBase;
use app\common\model\Category;
use app\common\model\DoctorInfoView;
use app\common\model\DoctorComment;
use app\common\model\DoctorInfo;
use app\common\model\Member;
use app\common\model\MemberDoctor;
use org\weixin\WXPayTools;
use think\Db;
use org\tools\SystemTool;
use think\facade\Log;
use app\common\model\ShoppingCar;
use app\common\model\DoctorShoppingCar;
use app\common\model\DoctorPay;

class Doctor extends IndexBase
{
	public function index(){
		$categoryId = $this->request->param('id',0);
		$categoryTitle = '在线医生';
		if($categoryId){
			$catrgory = Category::get($categoryId);
			$categoryTitle = $catrgory->name;
		}
		$this->assign('category_id',isset($catrgory->id)?$catrgory->id:0);
		$this->assign('ctitle',$categoryTitle);
		return $this->fetch();
	}
	
	/**
	 * ajax数据
	 */
	public function data(){
		$categoryId = $this->request->param('category_id',0);
		$page = $this->request->param('page',1);
		$where = ['status'=>1];
		if($categoryId){
			$where['category_id'] = $categoryId;
		}
		$doctors = DoctorInfoView::where($where)->order('sort','ASC')->page($page,5)->select();
		foreach ($doctors as &$doctor){
			$doctor['comment_url'] = url('doctor_comments',['doctor_id'=>$doctor['id']]);
			$doctor['chat_url'] = url('doctor_chat',['doctor_id'=>$doctor['id']]);
			$doctor['faceimgurl']=get_thumb_img($doctor['faceimgurl']);
		}
		return ['data'=>$doctors];
	}
	/**
	 * 对话
	 */
	public function chat(){
		$doctor_id = $this->request->param('doctor_id');
		if(!$doctor_id){
			$this->error('参数错误');
		}
		$doctor = DoctorInfoView::where(['id'=>$doctor_id])->find();
		if(!$doctor){
			$this->error('该医生不存在');
		}
		$this->assign('doctor',$doctor);
		// 是否收藏
		$collect = MemberDoctor::where(['member_id'=>$this->member['id'],'doctor_id'=>$doctor_id])->find();
		$this->assign('is_collected',$collect?1:0);
		return $this->fetch();
	}
	
	/**
	 * 查看医生评论
	 */
	public function comments(){
		$doctor_id = $this->request->param('doctor_id');
		if(!$doctor_id){
			$this->error('参数错误');
		}
		$doctor = DoctorInfoView::where(['id'=>$doctor_id])->find();
		if(!$doctor){
			$this->error('该医生不存在');
		}
		$this->assign('doctor',$doctor);
		
		$comments = DoctorComment::where(['doctor_id'=>$doctor_id])->order('create_time DESC')->select();
		$comments = $comments->toArray();
		foreach ($comments as &$data){
			$member = Member::where(['id'=>$data['member_id']])->find();
			$data['member_img'] = $member->headimgurl;
			$data['member_name'] = $member->name?:$member->nickname;
		}
		$this->assign('comments',$comments);
		return $this->fetch();
	}
	
	/**
	 * 对医生的评论
	 */
	public function comment(){
		$doctor_id = $this->request->param('doctor_id');
		if(!$doctor_id){
			$this->error('参数错误');
		}
		$doctor = DoctorInfoView::where(['id'=>$doctor_id])->find();
		if(!$doctor){
			$this->error('该医生不存在');
		}
		$this->assign('doctor',$doctor);
		
		// 是否收藏
		$collect = MemberDoctor::where(['member_id'=>$this->member['id'],'doctor_id'=>$doctor_id])->find();
		$this->assign('is_collected',$collect?1:0);
		
		return $this->fetch();
	}
	
	/**
	 * 评论保存
	 */
	public function commentPost(){
		$doctor_id = $this->request->post('doctor_id');
		$score = $this->request->post('score',5);
		$note = $this->request->post('note');
		if(!$note){
			$note = "该用户只评分没有评论。";
		}
		
		$find = DoctorComment::where(['doctor_id'=>$doctor_id,'member_id'=>$this->member['id']])->find();
		if($find){
			$this->error('您已评论过该医生');
		}
		$data = [
				'doctor_id'  => $doctor_id,
				'member_id'  => $this->member['id'],
				'note'       => $note,
				'score'      => $score,
				'create_time' => time()
		];
		
		$id = DoctorComment::create($data);
		if($id){
			$scores = DoctorComment::where(['doctor_id'=>$doctor_id])->sum('score');
			if($scores == 0){
				$priase = 100;
			}else{
				$count = DoctorComment::where(['doctor_id'=>$doctor_id])->count();
				if($count > 0){
					$priase = round($scores / ($count * 5),2) * 100;
				}else{
					$priase = 100;
				}
				DoctorInfo::where(['id'=>$doctor_id])->update(['praise'=>$priase,'update_time'=>time()]);
			}
			$this->success('评论成功');
		}
		$this->error('评论失败');
		
	}
	
	/**
	 * 获得聊天信息
	 */
	public function chatinfo(){
		$doctor_id = $this->request->param('doctor_id');
		$doctorInfo = DoctorInfoView::where(['id'=>$doctor_id])->find();
		if(!$doctorInfo){
			return json(['errcode'=>1,'errmsg'=>'医生信息不存在']);
		}
		return json(['errcode'=>0,'doctor'=>$doctorInfo,'member'=>$this->member]);
	}
	
	/**
	 * 获得聊天记录
	 */
	public function chatHistory(){
		$uid = $this->member['id'];
		$to_uid = $this->request->param('to_uid');
		if(!$uid || !$to_uid){
			return json(['errcode'=>1,'errmsg'=>'参数有误']);
		}
		
	}
	
	/**
	 * 收藏医生
	 */
	public function collect(){
		$doctor_id = $this->request->param('doctor_id');
		if(!$doctor_id){
			return json(['errcode'=>1,'errmsg'=>'参数有误']);
		}
		if(!$this->member || $this->isDoctor){
			return json(['errcode'=>1,'errmsg'=>'身份有误']);
		}
		
		$find = MemberDoctor::where(['member_id'=>$this->member['id'],'doctor_id'=>$doctor_id])->find();
		if($find){
			return json(['errcode'=>0,'errmsg'=>'ok']);
		}
		$id = MemberDoctor::insert(['member_id'=>$this->member['id'],'doctor_id'=>$doctor_id,'create_time'=>time()]);
		if($id){
			return json(['errcode'=>0,'errmsg'=>'ok']);
		}
		return json(['errcode'=>1,'errmsg'=>'收藏失败']);
		
	}
	
	/**
	 * 支付聊天信息金额
	 */
	public function pay(){
		
		$doctor_id = $this->request->param('doctor_id');
		$member_id = $this->member['id'];
		if(!$doctor_id || !$member_id){
			return ['errcode'=>1,'errmsg'=>'参数有误'];
		}
		$doctorInfo = DoctorInfoView::where('id',$doctor_id)->find();
		if(!$doctorInfo){
			return ['errcode'=>1,'errmsg'=>'医生信息不存在'];
		}
		$total_amount = $doctorInfo->advisory_price;
		if($total_amount <= 0){
			return ['errcode'=>2,'errmsg'=>'医生价格为0'];
		}
		$order_no = WXPayTools::createPayNo();
		$notify= url('wxpay_chat_notity','',true,true);
		$result=WXPayTools::unifiedOrder($this->openid, $order_no, $this->member['name'].'的咨询', 0.01*100,$notify,strtotime('+2 day'));
		if(!empty( $result['code']) && $result['code']==1){
			$insert = [
					'pay_number'    => $order_no,
					'doctor_id'     => $doctor_id,
					'member_id'     => $this->member['id'],
					'total_amount'  => $total_amount,
					'pay_type'      => 1,
					'pay_name'      => '微信支付',
					'note'          => '',
					'status'        => 0,
					'update_time'   => time(),
					'create_time'   => time()
			];
			$insert = Db::name('doctor_pay')->insert($insert);
			if(!$insert){
				return ['errcode'=>1,'errmsg'=>0];
			}
			$paydata = weixin()->wxpay($result['result']['prepay_id'],SystemTool::getWeixinPayInfo('partnerKey'));
			return json(['errcode'=>0,'paydata'=>$paydata]);
		}
		return json(['errcode'=>1,'errmsg'=>'未知错误']);
	}
	
	/**
	 * 支付成功回调
	 */
	public function paySuccess(){
		$doctor_id = $this->request->param('doctor_id');
		$member_id = $this->member['id'];
		$row = DoctorPay::where(['doctor_id'=>$doctor_id,'member_id'=>$member_id])->update(['status'=>1]);
		$data = $this->request->post('results');
		Log::write('-----------------pay_success---------------------');
		Log::write(json_encode($data));
		return json(['errcode'=>0,'errmsg'=>'ok']);
	}
	
	/**
	 * 购药中专连接
	 */
	public function buy(){
		$member_id = $this->request->param('member_id');
		if(!$member_id){
			$this->error('参数有误');
		}
		session('for_member_id',$member_id);
		return $this->redirect(url('medicine_list'));
	}
	
	/**
	 * 医生为患者加入购物车
	 * @return \think\response\Json
	 */
	public function addCar(){
		if($this->request->isAjax()){
			$id = $this->request->post('id');
			if(!$id){
				return json(['errcode'=>1,'errmsg'=>'参数有误']);
			}
			
			$data = [];
			
			$member_id = session('for_member_id');
			if(!$member_id){
				return json(['errcode'=>1,'errmsg'=>'请选择需要购药的患者']);
			}
			
			$member = Member::get($member_id);
			if(!$member){
				return json(['errcode'=>1,'errmsg'=>'患者不存在']);
			}
			
			$find = DoctorShoppingCar::where(['product_id'=>$id,'member_id'=>$member_id,'doctor_id'=>$this->member['id'],'status'=>0])->find();
			if($find){
				$count = $find->count+1;
				$status = DoctorShoppingCar::update(['count'=>$count],['id'=>$find->id]);
			}else{
				$data = [
						'product_id' => $id,
						'member_id'  => $member_id,
						'doctor_id'  => $this->member['id'],
						'count'      => 1,
						'update_time' => time(),
						'create_time' =>time()
				];
				$status = DoctorShoppingCar::insert($data);
			}
			
			if($status !== false){
				return json(['errcode'=>0,'errmsg'=>'ok']);
			}
			return json(['errcode'=>1,'errmsg'=>'加入清单失败']);
		}
	}
	
	/**
	 * 医生购物车
	 */
	public function carList(){
		$member_id = session('for_member_id');
		$data = [];
		if($member_id){
			$member = Member::get($member_id);
			if(!$member->name){
				$member->name = $member->nickname;
			}
			$this->assign('thisMember',$member);
			$data = DoctorShoppingCar::with('product')->where(['member_id'=>$member_id,'doctor_id'=>$this->member['id'],'status'=>0])->select();
			foreach ($data as &$v){
				$v['total_price'] = $v->count * $v->product->price;
				$v->product->left_price = $v->product->originnal_price - $v->product->price;
				$v['left_price'] = $v->count * $v->product->originnal_price - $v['total_price'];
			}
		}
		$this->assign('data',$data);
		return $this->fetch('car_list');
	}
	
	/**
	 * 医生购物车更新数量
	 */
	public function carCount(){
		$id = $this->request->param('id');
		$type = $this->request->param('type','plus');
		if(!$id || !$type){
			return json(['errcode'=>1,'errmsg'=>'参数错误']);
		}
		$car = DoctorShoppingCar::get($id);
		if(!$car){
			return json(['errcode'=>1,'errmsg'=>'购物车不存在']);
		}
		if($type == 'plus'){
			$row = DoctorShoppingCar::update(['count'=>$car->count+1,'update_time'=>time()],['id'=>$car->id]);
			if($row){
				return ['errcode'=>0];
			}
			return ['errcode'=>1];
		}else{
			$count = ($car->count - 1) > 0 ? ($car->count - 1) : 1;
			$row = DoctorShoppingCar::update(['count'=>$count,'update_time'=>time()],['id'=>$car->id]);
			if($row){
				return ['errcode'=>0];
			}
			return ['errcode'=>1];
		}
	}
	
	/**
	 * 发送给患者
	 */
	public function sendToMember(){
		$ids = $this->request->param('ids');
		if(!$ids){
			return json(['errcode'=>1,'errmsg'=>'参数错误']);
		}
		$member_id = session('for_member_id');
		if(!$member_id){
			return json(['errcode'=>1,'errmsg'=>'请选择需要购药的患者']);
		}
		
		$member = Member::get($member_id);
		if(!$member){
			return json(['errcode'=>1,'errmsg'=>'患者不存在']);
		}
		
		$cars = DoctorShoppingCar::where('status',0)->whereIn('id',$ids)->select();
		
		if(!$cars){
			return json(['errcode'=>1,'errmsg'=>'请为患者选择药品病加入购物车']);
		}
		$cars = $cars->toArray();
		try {
			Db::startTrans();
			foreach ($cars as $car){
				$car_id = $car['id'];
				unset($car['id']);
				$row = Db::name('shopping_car')->insert($car);
				if($row === false){
					throw new \Exception('数据失败');
				}
				Db::name('doctor_shopping_car')->where('id',$car_id)->update(['status'=>1]);
				
			}
			Db::commit();
			return json(['errcode'=>0,'errmsg'=>'ok','url'=>url('member_chat',['member_id'=>$member_id])]);
		}catch (\Exception $e){
			Db::rollback();
			return json(['errcode'=>1,'errmsg'=>$e->getMessage()]);
		}
		
		return json(['errcode'=>1,'errmsg'=>'发给患者失败']);
		
	}
}
