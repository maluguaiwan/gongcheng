<?php
namespace app\home\controller;

use app\common\controller\IndexBase;
use think\Controller;
use think\Request;

class Api extends Controller
{
     /**
 * 微信支付回调
 * type类型：本方法只有 1 、2
 * 0注册送优惠券
 * 1订单
 * 2咨询
 * 3人头费
 * 4系统赠送优惠券
 * type=1 或  type=2
 * 1、查询订单用户 是否有上级  member 关联 member_level
 *         a、没有上级，不做操作
 *         b、有上级 判断用户推荐等级
 *              （1）普通推荐商  普通（购药/咨询）比率给上级提成
 *              （2）Vip推荐商  Vip（购药/咨询）比率给上级提成
 *                 提成插入member_reward 表中
 *                 member_id 用户id
 *                 type 1订单2咨询
 *                 from_id 来源id （订单id / 咨询订单id）
 *                 reward_type 奖励类型 1优惠券，2现金  | 默认2
 *                 coupon_id 优惠券id
 *                 money 金额
 *                 update_time
 *                 create_time
 * 2、提成插入成功后 用户表member 余额和总额 同时增加
 */
     public function reward_order(Request $request){
         $out_trade_no=$request->param('out_trade_no');
         $type=$request->param('type');
//         var_dump(reward(1,2));//购药提成
//         var_dump(reward(2,4));//咨询提成
//         var_dump(reward(0,5));//注册送优惠券
//         var_dump(reward(3,6));//人头费





     }
     public function img_test(){
         $path='/uploads/admin/admin/20180808/1a8f731dcfaf9700298245b00b24c827.jpg';
         var_dump(get_thumb_img($path));
     }
}
