<?php
namespace org\weixin;


use org\weixin\wxpay\WxPayNotify;
use org\weixin\wxpay\WxPayOrderQuery;
use org\weixin\wxpay\WxPayApi;
use think\facade\Log;

class ChatNotify extends WxPayNotify {
	
	private $order_no;
	private $sign;
	
	public function __construct($config){
		WxPay::init($config);
	}
	
	// 查询订单
	public function Queryorder($transaction_id) {
	    $input = new WxPayOrderQuery();
	    $input->SetTransaction_id ( $transaction_id );
	    $result = WxPayApi::orderQuery ( $input );
	    if (array_key_exists ( "return_code", $result ) && array_key_exists ( "result_code", $result ) && $result["return_code"] == "SUCCESS" && $result["result_code"] == "SUCCESS") {
	        return true;
	    }
	    return false;
	}
	
	public function NotifyProcessor($data, &$msg, &$log) {
	    $order = db('doctor_pay')->where (['pay_number' => $data['out_trade_no']])->find();
	    if (! $order) {
	        $msg = "订单不存在";
	        $log['status'] = 0;
	        $log['errmsg'] = $msg;
	        return false;
	    }
	    $notfiyOutput = array ();
	    if (! array_key_exists ( "transaction_id", $data )) {
	        $msg = "输入参数不正确";
	        $log['status'] = 0;
	        $log['errmsg'] = $msg;
	        return false;
	    }
	    // 查询订单，判断订单真实性
	    if (! $this->Queryorder ( $data["transaction_id"] )) {
	        $msg = "订单查询失败";
	        $log['status'] = 0;
	        $log['errmsg'] = $msg;
	        return false;
	    }
	    return $order;
	}
	// 重写回调处理函数
	public function NotifyProcess($data, &$msg) {
		Log::write('wxpay_NotifyProcess');
	    $data['create_time'] = time ();
	    $log = array ('request' => serialize ( $data ),'status' => 1,'errmsg' =>$msg,'create_time'=>time());
	    $order = $this->NotifyProcessor ( $data, $msg, $log );
	    Log::write('wxpay_NotifyProcess_msg:'.$msg);
	    $logId=db('wxpay_notify_log')->insertGetId( $log );
	    Log::write('wxpay_notify_log_id:'.$logId);
	    Log::write('wxpay_notify_log_sql:'.db('wxpay_notify_log')->getLastSql());
	    if ($order) {
	        if(!db('wxpay_log')->where(['out_trade_no' => $data['out_trade_no']])->value('id')){
	            $data['order_id'] = $order['id'];
	            $data['status'] = 1;
	            $data['create_time'] = time();
	            $insert = db('wxpay_log')->insertGetId($data);
	            Log::write('wxpay_log_id:'.$insert);
	                // 处理订单
	            $row = db('doctor_pay')->where(['id'=>$order['id']])->update(['status'=>1,'pay_status'=>3,'update_time'=>time()]);
	            if($row !== false){
	            	// TODO 发放奖励
	            	reward(2,$order['id']);
	            }
	        }
	        return true;
	    } else {
	        return false;
	    }
	}
	
}