<?php

use common\weixin\Weixin;
use org\tools\SystemTool;
use think\facade\Log;

/**
 * 返回微信实例
 * @return \org\weixin\ServiceWeixin
 */
function weixin(){
	$wechat = SystemTool::getWeixinAppInfo();
	Log::write(json_encode($wechat,JSON_UNESCAPED_UNICODE));
	return \org\weixin\Weixin::getInstance ( $wechat );
}

/**
 * 校验手机号
 * @param string $mobile
 * @return boolean
 */
function isMobile($mobile){
	if($mobile && preg_match("/^1[34578]\d{9}$/", $mobile)) {
		return true;
	} else {
		return false;
	}
}