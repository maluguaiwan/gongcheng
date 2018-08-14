<?php
namespace app\home\controller;

use app\common\controller\IndexBase;
use app\common\model\MemberReward as MemberRewardModel;
use app\common\model\Member as MemberModel;
use think\Request;

class Recommend extends IndexBase
{
	/**
	 * 推广中心首页
	 */
	public function index(Request $request){
	    $member_id=$this->member['id'];
	    $model_reward=new MemberRewardModel();
	    $model_member=new MemberModel();
	    //总提成订单数量
        $tmp['reward_order_count']=$model_reward->where('member_id = "'.$member_id.'" and reward_type=2')->count();
        //下属推荐人
        $tmp['reward_parent_count']=$model_member->where('parent_id = "'.$member_id.'"')->count();
        //提成列表
        $start_time=$request->param('start_time');
        $end_time=$request->param('end_time');
        $where='A.member_id = "'.$member_id.'" and A.reward_type=2';
        if ($start_time && $end_time){
            $where.=' and A.create_time between "'.strtotime($start_time).'" and "'.strtotime($end_time).'"';
        }
        $list=$model_reward->alias('A')
            ->field('A.*,B.name,B.nickname,B.mobile,B.level_id,B.headimgurl')
            ->join('member B','A.member_id=B.id')
            ->where($where)
            ->order('A.create_time DESC')
            ->limit(0)
            ->select();
        $total_amount=0;
        if ($list){
            foreach ($list as $key=>$val){
                $description=get_reward_type_zh()[$val['type']].'获得'.$val['money'].'元';
                $list[$key]['description']=$description;
                $total_amount+=$val['money'];
            }
        }
        //模板赋值
        $this->assign('tmp',$tmp);
		$this->assign('total_amount',$total_amount);
		$this->assign('list',$list);
		return $this->fetch();
	}
	
	public function data(){
		$page = $this->request->param('page',1);
		
	}
}
