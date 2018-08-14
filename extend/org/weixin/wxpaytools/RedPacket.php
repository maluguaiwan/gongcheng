<?php

namespace org\weixin\wxpaytools;
use org\tools\System;
use org\weixin\WxPay;
use org\weixin\wxpay\WxPayException;
/**
 * 红包
 * @author lpdx111
 *
 */
class RedPacket {
	private $cid;
	public static function getInstance($cid){
		$redpacket=new RedPacket();
		$redpacket->cid=$cid;
		return $redpacket;
	}
	/**
	 * 红包
	 *
	 * @author lpdx111
	 *  开发日期 2015-4-28 下午2:48:14
	 *
	 */
	public  function send($openid, $amount, $act_name='活动', $wishing = '恭喜你获得一个红包', $remark = '点击领取红包') {
		$data = array ();
		$data['cid'] = $this->cid;
		$data['open_id'] = $openid;
		$data['create_time'] = time ();
		if(!request()->isCli()){
			$data['pay_uid'] = session ( '?uid' ) ? session ( 'uid' ) : 0;
		}
		$data['amount'] = $amount;
		$appInfo = System::payinfo ( $this->cid );
		$companyName=System::baseInfo($this->cid,'name');
		try {
			$wxpay = WxPay::init ( $appInfo );
			$res = $wxpay->sendRedpacket ( $openid, $amount, $act_name, $remark, $wishing, $companyName);
			if ($res['code'] == 1) {
				$data['status'] = 1;
				$data['errcode'] = 0;
				$data['errmsg'] = '成功';
				$data['order_no'] = $res['order_no'];
				$data['pay_id'] = $res['id'];
			} else if ($res['code'] == 4||$res['err'] == '请求已受理，请稍后使用原单号查询发放结果') {
				$data['status'] = 4;
				$data['errcode'] = 0;
				$data['errmsg'] = '查询结果';
				$data['order_no'] = $res['order_no'];
				$data['pay_id'] = $res['id'];
			}else {
				$data['status'] = 2;
				$data['errcode'] = $res['code'];
				$data['errmsg'] = $res['err'];
				$data['order_no'] = $res['order_no'];
				$data['pay_id'] = $res['id'];
			}
			$packet_id = db('wxpay_redpacket_log')->insertGetId($data);
			$data['packet_id'] = $packet_id;
			return $data;
		} catch (\Exception $e ) {
			$data['status'] = 2;
			$data['errcode'] = $e->getCode ();
			$data['errmsg'] = $e->getMessage ();
			$data['order_no'] = '';
			db('wxpay_redpacket_log')->insert($data);
			throw new WxPayException($e->getMessage(), $e->getCode());
		}
	}
	/**
	 * 检查红包发送状态
	 * @param string $orderNo
	 * @throws WxPayException
	 * 
	 */
	public function check($orderNo)
	{
		$appInfo = System::payinfo ( $this->cid );
		try {
			$wxpay = WxPay::init ( $appInfo );
			$res = $wxpay->fetchRedPacketStatus($orderNo);
			return $res;
		}catch (\Exception $e){
			throw new WxPayException($e->getMessage(), $e->getCode());
		}
	}
}

?>