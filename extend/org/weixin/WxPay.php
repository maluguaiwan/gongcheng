<?php
namespace org\weixin;
use org\weixin\wxpay\WxPayConfig;
use org\weixin\wxpay\WxPayRefund;
use org\weixin\wxpay\WxPayApi;
use org\weixin\wxpay\WxPayRefundQuery;
use org\weixin\wxpay\WxPayUnifiedOrder;
use org\weixin\wxpay\WxSendRedpack;
use org\weixin\wxpay\WxRedpackQuery;
use org\weixin\wxpay\WxPayException;
use org\weixin\wxpay\WxPromotionTtransfersTransfers;

class WxPay {
	public $_config = null;
	public $api = null;
	public $isInit = false;
	/**
	 * 初始化
	 *
	 * @param array $config
	 *        	partnerId = mchid
	 *        	partnerKey = 支付签名
	 *        	cert_file = apiclient_cert.pem
	 *        	sslkey_path = apiclient_key.pem
	 */
	static function init($config) {
		/* 初始化 */
		if ($config['is_wxpay'] != 1) {
			throw new WxPayException('没有开启微信支付', 0);
		}
		$obj = new WxPay ();
		$obj->_config = $config;
		foreach ( array ('appid','secret','partnerId','partnerKey','cert_file','sslkey_path' ) as $k ) {
			if (! isset ( $config[$k] )) {
				throw new WxPayException('微信支付设置错误！', 0);
			}
		}
		
		WxPayConfig::$APPID = $config['appid'];
		WxPayConfig::$APPSECRET = $config['secret'];
		WxPayConfig::$MCHID = $config['partnerId'];
		WxPayConfig::$KEY = $config['partnerKey'];
		WxPayConfig::$SSLCERT_PATH = ROOT_PATH . 'cert/' . $config['cert_file'];
		WxPayConfig::$SSLKEY_PATH = ROOT_PATH . 'cert/' . $config['sslkey_path'];
		
		$obj->isInit = true;
		return $obj;
	}
	/**
	 *
	 * @param string $open_id
	 *        	微信OPENID
	 * @param string $refund_no
	 *        	退款单号
	 * @param string $transaction_id
	 *        	微信支付单号
	 * @param string $order_no
	 *        	订单号
	 * @param int $total_fee
	 *        	订单总金额 单位分
	 * @param int $refund_fee
	 *        	退款金额 单位分
	 */
	function refund($open_id, $refund_no, $transaction_id, $order_no, $total_fee, $refund_fee) {
		$input = new WxPayRefund ();
		$input->SetTransaction_id ( $transaction_id );
		$input->SetOut_trade_no ( $order_no );
		$input->SetTotal_fee ( $total_fee );
		$input->SetRefund_fee ( $refund_fee );
		$input->SetOut_refund_no ( $refund_no );
		$input->SetOp_user_id ( WxPayConfig::$MCHID );
		$result = WxPayApi::refund ( $input );
		$return = $this->_parseResult ( $result );
		$id = $this->_log ( __FUNCTION__, $open_id, $refund_fee, $input->GetValues (), $result, $return, $refund_no );
		return array ('code' => $return['code'],'err' => $return['error'],'id' => $id,'order_no' => $order_no,'result' => $result );
	}
	function refundQuery($refund_id) {
		$input = new WxPayRefundQuery ();
		$input->SetRefund_id ( $refund_id );
		$result = WxPayApi::refundQuery ( $input );
		$return = $this->_parseResult ( $result );
		$id = $this->_log ( __FUNCTION__, '', 0, $input->GetValues (), $result, $return, $refund_id );
		return array ('code' => $return['code'],'err' => $return['error'],'id' => $id,'order_no' => $refund_id,'result' => $result );
	}
	
	/**
	 * 微信支付统一下单接口
	 */
	function unifiedOrder( $openid, $order_no, $body, $total_fee, $notifyUrl,$expire_time = 0, $tradeType = 'JSAPI') {
		$input = new WxPayUnifiedOrder ();
		$input->SetOpenid ( $openid );
		$input->SetOut_trade_no ( $order_no );
		$input->SetBody ( $body );
		$input->SetTotal_fee ( $total_fee );
		$input->SetTrade_type ( $tradeType );
		$input->SetTime_start ( date ( 'YmdHis' ) );
		if ($expire_time) {
			$input->SetTime_expire ( date ( 'YmdHis', $expire_time ) );
		}
		$input->SetNotify_url ($notifyUrl);
		$result = WxPayApi::unifiedOrder ( $input );
		$return = $this->_parseResult ( $result );
		$id = $this->_log ( __FUNCTION__, $openid, $total_fee, $input->GetValues (), $result, $return, $order_no );
		return array ('code' => $return['code'],'err' => $return['error'],'id' => $id,'order_no' => $order_no,'result' => $result );
	}
	
	/* 发放红包 */
	/*
	 * 1. 发送频率规则
	 * 　 ◆　每分钟发送红包数量不得超过1800个；
	 * 　 ◆　北京时间0：00-8：00不触发红包赠送；（如果以上规则不满足您的需求，请发邮件至wxhongbao@tencent.com获取升级指引）
	 * 2. 红包规则
	 * 　 ◆　单个红包金额介于[1.00元，200.00元]之间；
	 * 　 ◆　同一个红包只能发送给一个用户；（如果以上规则不满足您的需求，请发邮件至wxhongbao@tencent.com获取升级指引）
	 */
	function sendRedpacket($openid, $amount, $act_name, $remark, $wishing, $send_name = '', $nick_name = '') {
		$billNo = $this->getBillNo ();
		$input = new WxSendRedpack ();
		$input->setMch_billNo ( $billNo );
		$input->setRe_openid ( $openid );
		$amount = $amount * 100;
		$input->SetTotal_amount ( $amount );
		$send_name = $send_name ?  : $this->_config['name'];
		$nick_name = $nick_name ?  : $send_name;
		$input->SetNick_name ( $nick_name );
		$input->SetSend_name ( $send_name );
		$input->SetWishing ( $wishing );
		$input->SetAct_name ( $act_name );
		$input->SetRemark ( $remark );
		$result = WxPayApi::sendRedPack ( $input );
		$return = $this->_parseResult ( $result );
		$id = $this->_log ( __FUNCTION__, $openid, $amount, $input->GetValues (), $result, $return ,$billNo);
		return array ('code' => $return['code'],'err' => $return['error'],'id' => $id,'order_no' => $billNo);
	}
	function fetchRedPacketStatus($orderNo) {
		$input = new WxRedpackQuery ();
		$input->setMch_billNo ( $orderNo );
		$input->SetBill_type ( 'MCHT' );
		$result = WxPayApi::fetchRedPacketStatus ( $input );
		return $result;
		
	}
	
	/* 转账 */
	function transferAccounts($openid, $amount, $remark) {

		$billNo = $this->getBillNo ();
		$input = new WxPromotionTtransfersTransfers ();
		$input->SetPartner_trade_no ( $billNo );
		$input->SetOpenid ( $openid );
		$amount = $amount * 100;
		$input->SetAmount ( $amount );
		$input->SetDesc ( $remark );
		$result = WxPayApi::promotionTtransfers ( $input );
		$return = $this->_parseResult ( $result );
		$id = $this->_log ( __FUNCTION__, $openid, $amount, $input->GetValues (), $result, $return );
		return array ('code' => $return['code'],'err' => $return['error'],'id' => $id,'order_no' => $result['partner_trade_no'] );
		
	}
	function getBillNo() {
		return WxPayConfig::$MCHID . date ( 'YmdHis' ) . mt_rand ( 1000, 9999 );
	}
	protected function _parseResult($result) {
		if ($result['return_code'] == 'SUCCESS') {
			if ($result['result_code'] == 'SUCCESS') {
				return array ('code' => 1,'error' => '' );
			} else {
				if (isset($result['error_code']) && $result['error_code'] == 'SYSTEMERROR') {
					return array ('code' => 4,'error' => $result['err_code_des'] );
				} else {
					return array ('code' => 2,'error' => $result['err_code_des'] );
				}
			}
		} else {
			return array ('code' => 3,'error' => $result['return_msg'] );
		}
	}
	protected function _log($api, $openid, $amount, $body, $result, $return, $order_no = '') {
		$data = array ();
		$data['api'] = $api;
		$data['openid'] = $openid;
		$data['body'] = serialize ( $body );
		$data['result'] = serialize ( $result );
		$data['ctime'] = time ();
		$data['code'] = $return['code'];
		$data['error'] = $return['error'];
		$data['amount'] = $amount;
		$data['order_no'] = $order_no;
		return db ( 'wxpay_api_log' )->insertGetId ( $data );
	}
}