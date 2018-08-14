<?php
namespace app\home\controller;

use app\common\model\Member AS MemberModel;
use app\common\controller\IndexBase;
use app\common\model\MemberAddress;
use app\common\model\MemberBank;
use Qiniu\Http\Request;
use think\Db;
use app\common\model\MemberBankWater;
use org\tools\HttpTool;
use app\common\model\DoctorInfoView;
use app\common\model\Upload AS UploadModel;
use app\common\model\DoctorInfo;
use app\common\model\Message;
use app\common\model\MemberMessageView;
use app\common\model\MessageHistory;
use org\tools\StringTool;
use app\common\model\MemberNote;
use app\common\model\MemberCoupon;
use org\tools\SmsTool;
use think\facade\Log;

class Member extends IndexBase
{
	protected $is_userinfo = false;
	/**
	 * 会员中心首页
	 * @return mixed|string
	 */
	public function index(){
		$member = MemberModel::where(['openid'=>$this->openid])->find();
		if(!$member->name){
			$member->name = $member->nickname;
		}
		$this->assign('member',$member);
		
		// 查询未读消息
		$message_count = MessageHistory::where(['status'=>0,'to_uid'=>$this->member['id']])->count();
		$this->assign('message_count',$message_count?:0);
		return $this->fetch();
	}
	
	/**
	 * 会员绑定手机号
	 * @return mixed|string
	 */
	public function bind(){
		if($this->request->isPost()){
			$verify = $this->request->post('verify','');
			// TODO 验证码暂时不校验
			$name = $this->request->post('name');
			if(!$name){
				return json(['errcode'=>1,'errmsg'=>'真实姓名不能为空']);
			}
			$mobile = $this->request->post('mobile');
			if(!$mobile){
				return json(['errcode'=>1,'errmsg'=>'手机号不能为空']);
			}
			if(!isMobile($mobile)){
				return json(['errcode'=>1,'errmsg'=>'手机号格式不正确']);
			}
			
			$code = session('member_bind_sms_'.$this->member['id']);
			if(!$code || $verify != $code){
				return json(['errcode'=>1,'errmsg'=>'验证码不正确']);
			}
			
			
			$status = MemberModel::update(['name'=>$name,'mobile'=>$mobile,'status'=>1,'register_time'=>time()],['id'=>$this->member['id']]);
			if($status !== false){
				session('member_bind_sms_'.$this->member['id'],null);
				return json(['errcode'=>0,'errmsg'=>'ok']);
			}
			return json(['errcode'=>1,'errmsg'=>'绑定手机号失败']);
		}else{
			$member = MemberModel::where(['openid'=>$this->openid])->find();
			if(!$member->name){
				$member->name = $member->nickname;
			}
			$this->assign('member',$member);
			return $this->fetch();
		}
	}
	
	/**
	 * 会员绑定手机号发送短信
	 */
	public function bindSmsSend(){
		$mobile = $this->request->param('mobile');
		if(!$mobile){
			return ['errcode'=>1,'errmsg'=>'手机号不能为空'];
		}
		if(!isMobile($mobile)){
			return json(['errcode'=>1,'errmsg'=>'手机号格式不正确']);
		}
		$code = rand(1000,9999);
		$res = SmsTool::send($mobile,$code,$this->member['id']);
		if(is_string($res)){
			$res = json_decode($res,true);
			if(!empty($res['msg']) && $res['msg'] == 'OK'){
				session('member_bind_sms_'.$this->member['id'],$code);
				return ['errcode'=>0];
			}
		}else{
			if(!empty($res['msg']) && $res['msg'] == 'OK'){
				session('member_bind_sms_'.$this->member['id'],$code);
				return ['errcode'=>0];
			}
		}
		
		return ['errcode'=>1,'errmsg'=>'发送失败,联系系统管理员','res'=>$res];
	}
	
	/**
	 * 我的医生
	 * @return mixed|string
	 */
	public function doctor(){
		$count = MessageHistory::where(['to_uid'=>$this->member['id'],'status'=>0])->count();
		$this->assign('count',$count);
		return $this->fetch();
	}
	
	/**
	 * 会员地址管理
	 * @return mixed|string
	 */
	public function address(){
		if($this->request->isPost()){
			$post = $this->request->post();
			if(empty($post['member_id'])){
				return json(['errcode'=>1,'errmsg'=>'参数错误']);
			}
			$info = MemberAddress::where(['member_id'=>$post['member_id']])->order('update_time','DESC')->find();
			if($info){
				$status = MemberAddress::update($post,['id'=>$info['id']]);
			}else{
				$status = MemberAddress::create($post);
			}
			if($status !== false){
				return json(['errcode'=>0,'errmsg'=>'ok']);
			}
			return json(['errcode'=>1,'errmsg'=>'编辑失败']);
		}else{
			$member = MemberModel::where(['openid'=>$this->openid])->find();
			$this->assign('member',$member);
			/* 收款信息(按照单条信息) */
			$info = MemberAddress::where(['member_id'=>$member->id])->find();
			$this->assign('info',$info);
			return $this->fetch();
		}
	}
	
	/**
	 * 会员优惠券
	 */
	public function coupon(){
		$coupons = MemberCoupon::with('coupon')->where(['member_id'=>$this->member['id']])->select();
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
	
	/**
	 * 推荐会员页面(二维码)
	 * @return mixed|string
	 */
	public function recommendMember(){
		// 二维码连接
		$url = HttpTool::getDomain().'/?type=1&pid='.$this->member['id'];
		$this->assign('url',$url);
		return $this->fetch();
	}
	
	/**
	 * 绑定银行卡
	 * @return mixed|string
	 */
	public function bank(){
		if($this->request->isPost()){
			$post = $this->request->post();
			if(empty($post['member_id'])){
				return json(['errcode'=>1,'errmsg'=>'参数错误']);
			}
			
			if(empty($post['verify'])){
				return json(['errcode'=>1,'errmsg'=>'验证码不能为空']);
			}
			
			$code = session('member_bank_sms_'.$this->member['id']);
			if($code != $post['verify']){
				return json(['errcode'=>1,'errmsg'=>'验证码有误']);
			}
			
			$info = MemberBank::where(['member_id'=>$post['member_id']])->order('update_time','DESC')->find();
			if($info){
				$status = MemberBank::update($post,['id'=>$info['id']]);
			}else{
				$status = MemberBank::create($post);
			}
			if($status !== false){
				return json(['errcode'=>0,'errmsg'=>'ok']);
			}
			
			
			return json(['errcode'=>1,'errmsg'=>'编辑失败']);
		}else{
			$member = MemberModel::where(['openid'=>$this->openid])->find();
			$this->assign('member',$member);
			/* 收款信息(按照单条信息) */
			$info = MemberBank::where(['member_id'=>$member->id])->find();
			$this->assign('info',$info);
			return $this->fetch();
		}
	}
	
	/**
	 * 会员绑定手机号发送短信
	 */
	public function bankSmsSend(){
		$mobile = $this->request->param('mobile');
		$mobile = $this->member['mobile'];
		if(!$mobile){
			return ['errcode'=>1,'errmsg'=>'您没有绑定手机号，请先绑定手机号再绑定银行卡'];
		}
		$code = rand(1000,9999);
		$res = SmsTool::send($mobile,$code,$this->member['id']);
		
		$res = json_decode($res,true);
		if(!empty($res['msg']) && $res['msg'] == 'OK'){
			session('member_bank_sms_'.$this->member['id'],$code);
			return ['errcode'=>0];
		}
		
		return ['errcode'=>1,'errmsg'=>'发送失败,联系系统管理员','res'=>$res];
	}
	
	/**
	 * 会员提现
	 * @return mixed|string
	 */
	public function withdraw(){
		if($this->request->isPost()){
			$money = $this->request->post('money');
			$member = MemberModel::where(['openid'=>$this->openid])->find();
			if(!$member){
				return json(['errcode'=>1,'errmsg'=>'用户不存在']);
			}
			if(floatval($money) < 0){
				return json(['errcode'=>1,'errmsg'=>'提现金额不正确']);
			}
			if($money > $member['amount']){
				return json(['errcode'=>1,'errmsg'=>'提现金额不能超过可提现金额']);
			}
			$bank = MemberBank::where(['member_id'=>$member->id])->order('update_time','DESC')->find();
			if(!$bank){
				return json(['errcode'=>1,'errmsg'=>'未绑定银行卡不能提现']);
			}
			Db::startTrans();
			try {
				$status = MemberModel::where(['id'=>$member['id']])->setDec('amount',$money);
				$beforeAmount = $member->amount;
				if($status){
					$water = [
							'member_id' => $member->id,
							'bank_id'   => $bank->id,
							'before_amount' => $beforeAmount,
							'money'     => $money,
							'after_amount'  => ($beforeAmount-$money),
							'apply_time' => time(),
							'cretae_time' => time()
					];
					$create = MemberBankWater::create($water);
					if($create !== false){
						Db::commit();
						return json(['errcode'=>0,'errmsg'=>'ok']);
					}
					throw new \Exception('创建流水失败');
				}
				throw new \Exception('更新会员余额失败');
			}catch (\Exception $e){
				Db::rollback();
				return json(['errcode'=>1,'errmsg'=>$e->getMessage()]);
			}
		}else{
			$member = MemberModel::where(['openid'=>$this->openid])->find();
			$this->assign('member',$member);
			$isBindBank = false;
			$bank = MemberBank::where(['member_id'=>$member->id])->order('update_time','DESC')->find();
			if($bank){
				$isBindBank = true;
			}
			$this->assign('isBindBank',$isBindBank);
			return $this->fetch();
		}
	}
	
	/**
	 * 邀请医生注册
	 * @return mixed|string
	 */
	public function recommendDoctor(){
		$url = url('doctor_index','',true,true).'?type=2&pid='.$this->member['id'];
		$this->assign('url',$url);
		return $this->fetch();
	}
	
	public function doctorInfo(){
		if($this->request->isPost()){
			$request = $this->request->post();
			$id = $this->member['id'];
			$name = $request['name'] ? $request['name'] : '';
			$advisory_price = $request['advisory_price'] ? $request['advisory_price'] : '';
            $category_id = $request['category_id'] ? $request['category_id'] : '';
			$category_id = $request['category_id'] ? $request['category_id'] : '';
			$city = $request['city'] ? $request['city'] : '';
			$area = $request['area'] ? $request['area'] : '';
			$address = $request['address'] ? $request['address'] : '';
			$description = $request['description'] ? $request['description'] : '';
			// 诊所名称
			$clinic = $request['clinic'] ? $request['clinic']:'';
			// 联系电话
			$contact_mobile = $request['contact_mobile'] ? $request['contact_mobile'] : '';
			// 联系微信
			$contact_weixin = $request['contact_weixin'] ? $request['contact_weixin'] : '';
			// 特色疗法
			$tese = $request['tese'] ? $request['tese'] : '';
			// 个人照片
			$faceimgurl = $request['faceimgurl'] ? $request['faceimgurl']:'';
			// 个人简介
			$introduction = $request['introduction'] ? $request['introduction'] : '';
			// 医疗执业许可证
			$medical_license = $request['medical_license'] ? $request['medical_license'] :'';
			// 店面照片
			$clinic_photo = $request['clinic_photo'] ? $request['clinic_photo'] : '';
			
			$updateData = [
					'name' => $name,
					'advisory_price' => $advisory_price,
					'category_id' => $category_id,
					'city' => $city,
					'area' => $area,
					'address' => $address,
					'description' => $description,
					'clinic' => $clinic,
					'contact_mobile' => $contact_mobile,
					'contact_weixin' => $contact_weixin,
					'tese' => $tese,
					'faceimgurl' => $faceimgurl,
					'introduction' => $introduction,
					'medical_license' => $medical_license,
					'clinic_photo' => $clinic_photo
			];
			
			$find = DoctorInfo::where(['id'=>$id])->find();
			if($find){
				$status = DoctorInfo::where(['id'=>$id])->update($updateData);
			}else{
				$updateData['id'] = $id;
				$status =  DoctorInfo::insert($updateData);
			}
			if($status !== false){
				MemberModel::where(['id'=>$id])->update(['name'=>$name]);
				$this->success('更新成功,等待审核',url('member_index'));
			}else{
				$this->error('更新失败');
			}
			
		}else{
			Log::write('doctor_info'.__FILE__.__LINE__);
			Log::write('doctor_info_openid:'.$this->openid);
			
			$memberInfo = MemberModel::where(['openid'=>$this->openid])->find();
			if(empty($memberInfo)){
				$this->error('数据错误');
			}
			if($memberInfo->type != 2){
				$this->error('身份有误');
			}
			$memberInfo = $memberInfo->toArray();
			$doctor_info = DoctorInfo::where(['id'=>$memberInfo['id']])->find();
			if($doctor_info){
				$doctor_info = $doctor_info->toArray();
				$memberInfo = array_merge($memberInfo,$doctor_info);
			}
			$this->assign('info',$memberInfo);

			//医生类别
            $where = array();
            $where[]=['type','eq',2];
            $where[]=['is_delete','neq',1];
            //查询数据
            $list_cat=model('Category')->loadList($where);
            //模板传值
            $this->assign('list_cat',$list_cat);

			return $this->fetch();
		}
	}
	
	
	/**
	 * 上传文件
	 */
	public function uploadImage(){
		$model =new UploadModel();
		return json($model->upfile('images','file'));
	}
	
	/**
	 * 我的患者（医生版）
	 * @return mixed|string
	 */
	public function myMember(){
		$count = MessageHistory::where(['to_uid'=>$this->member['id'],'status'=>0])->count();
		$this->assign('count',$count);
		return $this->fetch('my_member');
	}
	
	/**
	 * 我的患者
	 */
	public function myMemberData(){
		$name = $this->request->param('name');
		$page = $this->request->param('page',1);
		
		$where['to_uid'] = $this->member['id'];
		if($name){
			$where['name'] = ['like','%'.$name.'%'];
		}
		$datas = MemberMessageView::where($where)->order('create_time DESC')->page($page,5)->select();
		if(!empty($datas)){
			$datas = $datas->toArray();
			foreach ($datas as &$data){
				$data['link'] = url('member_chat',['member_id'=>$data['id']]);
				$newMessage = MessageHistory::where(['uid'=>$data['id'],'to_uid'=>$this->member['id'],'status'=>0])->find();
				if(!$newMessage){
					$data['new_message'] = 0;
				}else{
					$data['new_message'] = 1;
				}
				$content = !empty($newMessage['message'])?$newMessage['message']:'';
				$data['last_message'] = StringTool::msubstr($content,0,8);
			}
		}
		return json(['data'=>$datas]);
	}
	
	/**
	 * 我的医生（会员版）
	 */
	public function myDoctorData(){
		$name = $this->request->param('name');
		$page = $this->request->param('page',1);
		
		if($name){
			$datas = MemberMessageView::where('to_uid|uid',$this->member['id'])->whereLike('name','%'.trim($name).'%')->order('create_time DESC')->page($page,5)->select();
		}else{
			$datas = MemberMessageView::where('to_uid|uid',$this->member['id'])->order('create_time DESC')->page($page,5)->select();
		}
		
		if(!empty($datas)){
			$datas = $datas->toArray();
			foreach ($datas as &$data){
			    
			    if($data['uid'] == $this->member['id']){
			        $data['link'] = url('doctor_chat',['doctor_id'=>$data['to_uid']]);
			        $doctorInfo = DoctorInfoView::where(['id'=>$data['to_uid']])->find();
			        $data['name'] = $doctorInfo['name'];
			        $data['headimgurl'] = $doctorInfo['faceimgurl'];
			        $newMessage = MessageHistory::where(['uid'=>$data['to_uid'],'to_uid'=>$this->member['id'],'status'=>0])->find();
			        if(!$newMessage){
			            $data['new_message'] = 0;
			        }else{
			            $data['new_message'] = 1;
			        }
			        $content = !empty($newMessage['message'])?$newMessage['message']:'';
			        $data['last_message'] = StringTool::msubstr($content,0,8);
			    }else{
			        $data['link'] = url('doctor_chat',['doctor_id'=>$data['id']]);
			        $newMessage = MessageHistory::where(['uid'=>$data['id'],'to_uid'=>$this->member['id'],'status'=>0])->find();
			        if(!$newMessage){
			            $data['new_message'] = 0;
			        }else{
			            $data['new_message'] = 1;
			        }
			        $content = !empty($newMessage['message'])?$newMessage['message']:'';
			        $data['last_message'] = StringTool::msubstr($content,0,8);
			    }
			}
		}
		return json(['data'=>$datas]);
	}
	
	/**
	 * 患者咨询
	 */
	public function memberChat(){
		$member_id = $this->request->param('member_id');
		if(!$member_id){
			$this->error('参数错误');
		}
		$member = MemberModel::get($member_id);
		if(!$member){
			$this->error('数据错误');
		}
		$member = $member->toArray();
		if(empty($member['name'])){
			$member['name'] = $member['nickname'];
		}
		$this->assign('member',$member);
		
		return $this->fetch('chat');
	}
	/**
	 * 患者医生基本信息
	 * @return \think\response\Json
	 */
	public function memberChatInfo(){
		$member_id = $this->request->param('member_id');
		$memberInfo = MemberModel::where(['id'=>$member_id])->find();
		if(!$memberInfo){
			return json(['errcode'=>1,'errmsg'=>'用户信息不存在']);
		}
		$doctorInfo = DoctorInfoView::where(['id'=>$this->member['id']])->find();
		return json(['errcode'=>0,'doctor'=>$doctorInfo,'member'=>$memberInfo]);
	}
	
	/**
	 * 会员病例列表
	 */
	public function noteList(){
		$member_id = $this->request->param('member_id');
		if(!$member_id){
			$this->error('参数错误');
		}
		
		if(!$this->member && !$this->isDoctor){
			$this->error('对不起您没有权限访问');
		}
		
		$memberInfo = MemberModel::get($member_id);
		if(!$memberInfo){
			$this->error('会员信息不存在');
		}
		if(!$memberInfo->name){
			$memberInfo->name = $memberInfo->nickname;
		}
		
		$list = MemberNote::where(['member_id'=>$member_id,'doctor_id'=>$this->member['id']])->select();
		$this->assign('info',$memberInfo);
		$this->assign('list',$list);
		return $this->fetch();
	}
	
	/**
	 * 备注页面
	 * @return \think\response\Json|mixed|string
	 */
	public function note(){
		if($this->request->isPost()){
			$member_id = $this->request->param('member_id');
			if(!$member_id){
				return json(['errcode'=>1,'errmsg'=>'参数错误']);
			}
			$note = $this->request->param('note');
			if(!$note){
				return json(['errcode'=>1,'errmsg'=>'备注不能为空']);
			}
			$row = MemberNote::insert([
					'member_id'   => $member_id,
					'doctor_id'   => $this->member['id'],
					'description' => $note,
					'create_time' => time()
			]);
			if($row){
				return json(['errcode'=>0,'errmsg'=>'ok']);
			}
			return json(['errcode'=>1,'errmsg'=>'备注失败']);
		}else{
			$member_id = $this->request->param('member_id');
			$memberInfo = MemberModel::get($member_id);
			if(!$memberInfo->name){
				$memberInfo->name = $memberInfo->nickname;
			}
			$this->assign('info',$memberInfo);
			return $this->fetch();
		}
	}
	
	/**
	 * 邀请医生首页
	 */
	public function doctorIndex(){
		$type = $this->request->param('type');
		$pid  = $this->request->param('pid');
		$this->redirect(url('doctor_info',['type'=>$type,'pid'=>$pid]));
	}

	/**
     * 提现记录
     */
	public function memberBankWater(){
        $member_id=$this->member['id'];
        $start_time=$this->request->param('start_time');
        $end_time=$this->request->param('end_time');
	    $model=new MemberBankWater();
	    $where='member_id="'.$member_id.'"';
        if ($start_time && $end_time){
            $where.=' and create_time between "'.strtotime($start_time).'" and "'.strtotime($end_time).'"';
        }
	    $list=$model->field('money,if_pay,create_time')->where($where)->order('create_time')->select();
	    $this->assign('list',$list);
	    $this->assign('total_amount',$model->where($where)->sum('money'));
	    return $this->fetch();
    }
}
