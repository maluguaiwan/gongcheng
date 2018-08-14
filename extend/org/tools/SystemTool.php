<?php
namespace org\tools;

use think\facade\Cache;

class SystemTool{
	
	public static function getWeixinAppInfo(){
		$info = [];
		if(config('weixin.appid')){
			$info['appid'] = config('weixin.appid');
		}
		if(config('weixin.secret')){
			$info['secret'] = config('weixin.secret');
		}
		return $info;
	}
	
	public static function getWeixinPayInfo($field = "*"){
		$info=Cache::get('weixinpayinfo');
		if(!$info){
			$wechat = self::getWeixinAppInfo();
			$wxpay = config('weixin.wxpay');
			
			if(!empty($wechat) && !empty($wxpay)){
				$info = array_merge($wechat,$wxpay);
			}
		
			Cache::set('weixinpayinfo',$info);
		}
		if($field&&$field!='*'){
			return 	isset($info[$field])?$info[$field]:null;
		}
		return $info;
	}
	
	/**
	 * 推荐奖励
	 * @param number $member
	 * @param number $from
	 * @param number $type
	 * @param vachar $note
	 */
	public static function memberReward($memberId,$fromId,$type,$note){
		
	}
	
}