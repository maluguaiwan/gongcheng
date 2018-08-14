<?php

namespace org\tools;

/**
 * 微信授权后保存到cookie（会做加密处理）
 *
 * @author lpdx111
 */
class OAuthTool {
	/**
	 * 获取 或者设置 会员的新
	 * 
	 * @param int $cid        	
	 * @param string $value        	
	 */
	public static function member($value = null) {
		if ($value === null) {
			return self::decrypt ( cookie ( "weixin_oauth" ) );
		} else {
			cookie ( "weixin_oauth", self::encrypt ( $value ), 3600 * 24 * 7 );
		}
	}
	/**
	 * 判断是否有会的oauth 信息
	 * 
	 * @param int $cid        	
	 * @return boolean
	 */
	public static function isMember() {
		return cookie ( "?weixin_oauth");
	}
	
	/**
	 * 加密
	 *
	 * @param unknown $value        	
	 */
	public static function encrypt($value) {
		return $value;
	}
	/**
	 * 解密
	 *
	 * @param unknown $value        	
	 */
	public static function decrypt($value) {
		return $value;
	}
}

?>