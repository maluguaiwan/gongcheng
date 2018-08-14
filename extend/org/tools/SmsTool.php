<?php
namespace org\tools;


use org\Ucpaas;

class SmsTool{
	
	public static function send($mobile,$code,$uid){
		if(!$mobile){
			return ['errcode'=>0,'errmsg'=>'手机号有误'];
		}
		if(!self::checkMobile($mobile)){
			return ['errcode'=>0,'errmsg'=>'手机号格式错误'];
		}
		
		$appId = config('sms.appID');
		$options['accountsid'] = config('sms.accountSid');
		$options['token']      = config('sms.authToken');
		$ucpass = new Ucpaas($options);
		try {
			return $ucpass->SendSms($appId, '358916',$code,$mobile,$uid);
		} catch (\Exception $e){
			return ['errcode'=>1,'errmsg'=>$e->getMessage()];
		}
		
	}
	
	public static function checkMobile($mobile){
		if (preg_match('/(^(13\d|14\d|15\d|16\d|17\d|18\d|19\d)\d{8})$/', $mobile)) {
			return true;
		} else {
			if (preg_match('/^\d{1,4}-\d{5,11}$/', $mobile)) {
				if (preg_match('/^\d{1,4}-0+/', $mobile)) {
					//不能以0开头
					return false;
				}
				
				return true;
			}
			
			return false;
		}
	}
	
}

?>