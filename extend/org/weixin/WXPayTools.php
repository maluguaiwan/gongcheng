<?php

namespace org\weixin;
use org\weixin\wxpay\WxPayException;
use org\tools\StringTool;
use org\tools\SystemTool;
class WXPayTools {
	/**
	 * 创建订单号
	 */
	static function createOrderNo() {
		$yCode = array ('A','B','C','D','E','F','G','H','I','J' );
		$orderSn = $yCode[intval ( date ( 'Y' ) ) - 2016] . strtoupper ( dechex ( date ( 'm' ) ) ) . date ( 'd' ) . substr ( time (), - 5 ) . substr ( microtime (), 2, 5 ) . sprintf ( '%02d', rand ( 0, 99 ) );
		return $orderSn;
	}
	/**
	 * 支付信息
	 * @return string
	 */
	static function createPayNo() {
		$yCode = array ('A','B','C','D','E','F','G','H','I','J' );
		$orderSn = $yCode[intval ( date ( 'Y' ) ) - 2015] . strtoupper ( dechex ( date ( 'm' ) ) ) . date ( 'd' ) . substr ( time (), - 5 ) . substr ( microtime (), 2, 5 ) . sprintf ( '%02d', rand ( 0, 99 ) );
		return $orderSn;
	}
	/**
	 * 创建SN码
	 * 
	 * @param sn $id        	
	 * @param
	 *        	order id $order_id
	 * @return string
	 */
	static function createSNNo($cid) {
		while(true){
			$orderno=StringTool::randString(12, 1);
			$data=['cid'=>$cid,'sn'=>$orderno];
			$result=db('order_sn')->insert($data);
			if($result){
				return $orderno;
			}
		}
	}
	/**
	 * 生成验证串
	 * 
	 * @param unknown $params        	
	 * @param unknown $key        	
	 */
	static function createSign($params, $key) {
		ksort ( $params, SORT_STRING );
		$str = '';
		foreach ( $params as $k => $v ) {
			if ($str) {
				$str = $str . "&$k=$v";
			} else {
				$str = "$k=$v";
			}
		}
		$str = $str . "&key=$key";
		$sign = strtoupper ( md5 ( $str ) );
		return $sign;
	}
	static function create_noncestr($length = 16) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for($i = 0; $i < $length; $i ++) {
			$str .= substr ( $chars, mt_rand ( 0, strlen ( $chars ) - 1 ), 1 );
			// $str .= $chars[ mt_rand(0, strlen($chars) - 1) ];
		}
		return $str;
	}
	public static function unifiedOrder($openid, $order_no, $body, $total_fee,$notifyUrl, $expire_time) {
		$appInfo = SystemTool::getWeixinPayInfo();
		$wxpay = WxPay::init ( $appInfo );
		$result = $wxpay->unifiedOrder ( $openid, $order_no, $body, $total_fee,$notifyUrl, $expire_time );
		return $result;
	}
	/**
	 *
	 * @param string $cid
	 *        	cid
	 * @param array $order
	 *        	订单
	 * @param string $refund_no
	 *        	退款单号
	 * @param double $refund_fee
	 *        	金额
	 * @param $refund_two 是否二次发起退款        	
	 */
	public static function refund($order,$transaction_id, $refund_no, $refund_fee, $refund_two = false) {
		$appInfo = SystemTool::getWeixinPayInfo();
		$wxpay = WxPay::init ( $appInfo );
		$result = $wxpay->refund ( $order['openid'], $refund_no, $transaction_id, $order['order_no'], $order['amount'] * 100, $refund_fee * 100 );
		$data = array ('order_id' => $order['id'],'order_model' => 'order','openid' => $order['openid'],'refund_time' => time () );
		if ($result['code'] == 1) {
			$data = array_merge ( $data, $result['result'] );
		}
		if (! $refund_two) {
			db ( 'wxpay_refund_log' )->insertGetId ( $data );
		} else {
			db ( 'wxpay_refund_log' )->where ( array ('out_refund_no' => $refund_no ) )->update ( $data );
		}
		return $result;
	}
	public static function refundQuery($refund_id) {
		$appInfo = SystemTool::getWeixinPayInfo();
		$wxpay = WxPay::init ( $appInfo );
		$result = $wxpay->refundQuery ( $refund_id );
		$res = $result['result'];
		db ( 'wxpay_refund_log' )->where ( array ('cid' => $cid,'refund_id' => $refund_id ) )->update ( array ('refund_result_status' => $res['refund_status_0'],'refund_result_recv_accout' => $res['refund_recv_accout_0'] ) );
		return $result;
	}
	/**
	 * 红包
	 *
	 * @author lpdx111
	 *         开发日期 2015-4-28 下午2:48:14
	 *        
	 */
	public static function redPacket($openid, $amount,  $wishing = '恭喜你获得一个红包', $act_name = '活动',$remark = '点击领取红包') {
		$data = array ();
		$data['open_id'] = $openid;
		$data['create_time'] = time ();
		$data['pay_uid'] = session ( '?uid' ) ? session ( 'uid' ) : 0;
		$data['amount'] = $amount;
		$appInfo = SystemTool::getWeixinAppInfo();
		$companyName=config('weixin.company_name');
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
			db('wxpay_redpacket_log')->insert($data);
			return $res;
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
	 * 转账
	 */
	public static function transferAccounts($openid, $agentid, $amount, $remark = '转账') {
		$data = array ();
		$data['open_id'] = $openid;
		$data['create_time'] = time ();
		$data['act_type'] = '';
		$data['act_id'] = 0;
		$data['act_title'] = '';
		$data['amount'] = $amount;
		$data['pay_uid'] = session ( '?uid' ) ? session ( 'uid' ) : 0;
		$data['status'] = 0;
		$data['errcode'] = 0;
		$data['errmsg'] = '';
		$data['order_no'] = '';
		$data['pay_type'] = 1; // 转账
		$payout_id = db ( 'wxpayout_history' )->insertGetId ( $data );
		if (! $openid) {
			return array ('code' => 0,'err' => '无法获取身份信息' );
		}
		if ($payout_id) {
			$appInfo = SystemTool::getWeixinPayInfo();
			$wxpay = WxPay::init ( $appInfo );
			if ($wxpay) {
				$res = $wxpay->transferAccounts ( $openid, $amount, $remark );
				if ($res['code'] == 1) {
					$data['status'] = 1;
					$data['errcode'] = 0;
					$data['errmsg'] = '成功';
					$data['order_no'] = $res['order_no'];
					$data['pay_id'] = $res['id'];
				} else {
					$data['status'] = 2;
					$data['errcode'] = $res['code'];
					$data['errmsg'] = $res['err'];
					$data['order_no'] = $res['order_no'];
					$data['pay_id'] = $res['id'];
				}
				db( 'wxpayout_history')->where(['id'=>$payout_id])->update($data);
				return $res;
			}
			return array ('code' => 0,'err' => '未开启微信支付功能或者支付api版本不正确!' );
		}
		return array ('code' => 0,'err' => '保存支付记录出错' );
	}
	
	/**
	 * 查询红包状态
	 * @param tring $orderNo  订单号
	 * @return \org\weixin\wxpay\multitype:
	 */
	public static function fetchRedPacketStatus($orderNo) {
		$appInfo = SystemTool::getWeixinAppInfo();
		$wxpay = WxPay::init ( $appInfo );
		$status = $wxpay->fetchRedPacketStatus ( $orderNo );
		return $status;
	}
}