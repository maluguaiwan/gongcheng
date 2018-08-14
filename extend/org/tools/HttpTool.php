<?php

namespace org\tools;

use think\Request;

class HttpTool {
	
	public static function getDomain(){
		$host = request()->host();
		return $host;
	}
	
	public static function getClientIp($type = 0) {
		$type = $type ? 1 : 0;
		static $ip = NULL;
		if ($ip !== NULL) return $ip[$type];
		if (isset ( $_SERVER['HTTP_X_FORWARDED_FOR'] )) {
			$arr = explode ( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );
			$pos = array_search ( 'unknown', $arr );
			if (false !== $pos) unset ( $arr[$pos] );
			$ip = trim ( $arr[0] );
		} elseif (isset ( $_SERVER['HTTP_CLIENT_IP'] )) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (isset ( $_SERVER['REMOTE_ADDR'] )) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		// IP地址合法验证
		$long = ip2long ( $ip );
		$ip = $long ? array ($ip,$long ) : array ('0.0.0.0',0 );
		return $ip[$type];
	}
	private static function strexists($string, $find) {
		return ! (strpos ( $string, $find ) === FALSE);
	}
	
	/**
	 * curl 请求
	 *
	 * @param string $url        	
	 * @param string $post
	 *        	post 的数据
	 * @param array $extra        	
	 * @param number $timeout
	 *        	超时
	 * @param boolean $header
	 *        	是否返回头
	 */
	private static function request($url, $post = '', $headers = array(), $timeout = 60, $header = true) {
		$ch = curl_init ();
		$curl_url = $url;
		curl_setopt ( $ch, CURLOPT_URL, $curl_url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_HEADER, $header );
		if ($post) {
			curl_setopt ( $ch, CURLOPT_POST, 1 );
			if (is_array ( $post )) {
				$post = http_build_query ( $post );
			}
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post );
		}
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
		curl_setopt ( $ch, CURLOPT_TIMEOUT, $timeout );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
		curl_setopt ( $ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1 );
		curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1' );
		if (! empty ( $headers ) && is_array ( $headers )) {
			curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
		}
		$data = curl_exec ( $ch );
		// $status = curl_getinfo($ch);
		// $errno = curl_errno($ch);
		// $error = curl_error($ch);
		curl_close ( $ch );
		
		if ($header) {
			return self::response_parse ( $data );
		} else {
			return array ('content' => $data );
			// 'status' => $status,
			// 'errno' => $errno,
			// 'error' => $error
		}
	}
	private static function response_parse($data) {
		$rlt = array ();
		$pos = strpos ( $data, "\r\n\r\n" );
		$split1[0] = substr ( $data, 0, $pos );
		$split1[1] = substr ( $data, $pos + 4, strlen ( $data ) );
		
		$split2 = explode ( "\r\n", $split1[0], 2 );
		preg_match ( '/^(\S+) (\S+) (\S+)$/', $split2[0], $matches );
		$rlt['code'] = $matches[2];
		$rlt['status'] = $matches[3];
		$rlt['responseline'] = $split2[0];
		$header = explode ( "\r\n", $split2[1] );
		$isgzip = false;
		foreach ( $header as $v ) {
			$row = explode ( ':', $v );
			$key = trim ( $row[0] );
			$value = trim ( $row[1] );
			if (is_array ( $rlt['headers'][$key] )) {
				$rlt['headers'][$key][] = $value;
			} elseif (! empty ( $rlt['headers'][$key] )) {
				$temp = $rlt['headers'][$key];
				unset ( $rlt['headers'][$key] );
				$rlt['headers'][$key][] = $temp;
				$rlt['headers'][$key][] = $value;
			} else {
				$rlt['headers'][$key] = $value;
			}
			if (! $isgzip && strtolower ( $key ) == 'content-encoding' && strtolower ( $value ) == 'gzip') {
				$isgzip = true;
			}
		}
		if ($isgzip && function_exists ( 'gzdecode' )) {
			$rlt['content'] = gzdecode ( $split1[1] );
		} else {
			$rlt['content'] = $split1[1];
		}
		$rlt['meta'] = $data;
		return $rlt;
	}
	
	/**
	 * HTTP GET 请求
	 *
	 * @param string $url
	 *        	URL
	 * @param boolean $header
	 *        	是否获取header
	 */
	public static function get($url, $headers = array(), $fetchHeader = false) {
		return self::request ( $url, null, $headers, 60, $fetchHeader );
	}
	
	/**
	 * HTTP POST 请求
	 *
	 * @param string $url
	 *        	URL
	 * @param array|string $data
	 *        	POST 的数据
	 * @param boolean $header
	 *        	是否获取header
	 */
	public static function post($url, $data, $headers = array(), $fetchHeader = false) {
		$_headers = array ('Content-Type' => 'application/x-www-form-urlencoded' );
		$headers = array_merge ( $_headers, $headers );
		return self::request ( $url, $data, $headers, 60, $fetchHeader );
	}
	public static function upload($url, $data) {
		$ch = curl_init ( $url );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_TIMEOUT, 50 );
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 50 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_POST, true );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
		curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt ( $ch, CURLOPT_MAXREDIRS, 3 );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
		$result = curl_exec ( $ch );
		// echo var_export($data, 1) . "\r\n";
		// echo var_export(curl_errno($ch), 1) . "\r\n";
		// echo var_export(curl_error($ch), 1) . "\r\n";
		// echo var_export(curl_getinfo($ch), 1) . "\r\n";
		curl_close ( $ch );
		return $result;
	}
	
	// 线程并发抓取函数mfetch：
	/**
	 * 多线程执行CURL
	 *
	 * @param array $urls
	 *        	要执行的URL 数组
	 *        	$urls=[['url'=>'www.ooyyee.com','params'=>[]]]
	 *        	
	 * @param String $method
	 *        	'get' or 'post'
	 * @param number $usleep
	 *        	每次间隔时间 微秒
	 * @return array
	 */
	private static function mutilRequest($urls = array(), $method, $usleep = 100000) {
		$mh = curl_multi_init (); // 始化一个curl_multi句柄
		$handles = array ();
		foreach ( $urls as $key => $param ) {
			$ch = curl_init (); // 始化一个curl句柄
			$url = isset ( $param["url"] ) ? $param["url"] : '';
			$data = isset ( $param["params"] ) ? $param["params"] : array ();
			if (strtolower ( $method ) === "get") {
				// 据method参数判断是post还是get方式提交数据
				$url = "$url?" . http_build_query ( $data ); // et方式
			} else {
				curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data ); // ost方式
			}
			curl_setopt ( $ch, CURLOPT_URL, $url );
			curl_setopt ( $ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0 );
			curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt ( $ch, CURLOPT_HEADER, 0 );
			curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 60 );
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
			curl_setopt ( $ch, CURLOPT_TIMEOUT, 60 );
			curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
			if(isset($_SERVER["HTTP_USER_AGENT"])){
				curl_setopt ( $ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"] );
			}
			curl_multi_add_handle ( $mh, $ch );
			$handles[( int ) $ch] = $key;
			// andles数组用来记录curl句柄对应的key,供后面使用，以保证返回的数据不乱序。
		}
		$running = null;
		$status = null;
		$result = array (); // url数组用来记录各个curl句柄的返回值
		do { // 起curl请求，并循环等等1/100秒，直到引用参数"$running"为0
			usleep ( $usleep );
			curl_multi_exec ( $mh, $running );
			while ( ($ret = curl_multi_info_read ( $mh )) !== false ) {
				// 环读取curl返回，并根据其句柄对应的key一起记录到$curls数组中,保证返回的数据不乱序
				$result[$handles[( int ) $ret["handle"]]] = $ret;
			}
		} while ( $running > 0 );
		foreach ( $result as $key => &$val ) {
			$val["result"] = curl_multi_getcontent ( $val["handle"] );
			curl_close ( $val["handle"] );
			curl_multi_remove_handle ( $mh, $val["handle"] ); // 除curl句柄
		}
		curl_multi_close ( $mh ); // 闭curl_multi句柄
		ksort ( $result );
		return $result;
	}
	
	/**
	 * 多线程执行CURL GET
	 *
	 * @param array $urls
	 *        	要执行的URL 数组
	 *        	$urls=[['url'=>'www.ooyyee.com','params'=>[]]]
	 * @param number $usleep
	 *        	每次间隔时间 微秒
	 * @return array
	 */
	public static function mutilGet(array $urls, $usleep = 10000) {
		return self::mutilRequest ( $urls, 'get', $usleep );
	}
	/**
	 * 多线程同时执行上传
	 *
	 * @param array $urls
	 *        	要执行的URL 数组
	 *        	$urls=[['url'=>'www.ooyyee.com','params'=>[]]]
	 * @param number $usleep
	 *        	每次间隔时间 微秒
	 * @return array
	 */
	public static function mutilUpload(array $urls, $usleep = 10000) {
		$mh = curl_multi_init (); // 始化一个curl_multi句柄
		$handles = array ();
		foreach ( $urls as $key => $param ) {
			$ch = curl_init (); // 始化一个curl句柄
			$url = isset ( $param["url"] ) ? $param["url"] : '';
			$data = isset ( $param["params"] ) ? $param["params"] : array ();
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data ); // ost方式
			curl_setopt ( $ch, CURLOPT_URL, $url );
			curl_setopt ( $ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0 );
			curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt ( $ch, CURLOPT_HEADER, 0 );
			curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 30 );
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
			curl_setopt ( $ch, CURLOPT_TIMEOUT, 30 );
			curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
			curl_setopt ( $ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"] );
			curl_multi_add_handle ( $mh, $ch );
			$handles[( int ) $ch] = $key;
		}
		$running = null;
		$status = null;
		$result = array (); // url数组用来记录各个curl句柄的返回值
		do { // 起curl请求，并循环等等1/100秒，直到引用参数"$running"为0
			usleep ( $usleep );
			curl_multi_exec ( $mh, $running );
			while ( ($ret = curl_multi_info_read ( $mh )) !== false ) {
				$result[$handles[( int ) $ret["handle"]]] = $ret;
			}
		} while ( $running > 0 );
		foreach ( $result as $key => &$val ) {
			$val["result"] = curl_multi_getcontent ( $val["handle"] );
			curl_close ( $val["handle"] );
			curl_multi_remove_handle ( $mh, $val["handle"] ); // 除curl句柄
		}
		curl_multi_close ( $mh ); // 闭curl_multi句柄
		ksort ( $result );
		return $result;
	}
	
	/**
	 * 多线程执行CURL POST
	 *
	 * @param array $urls
	 *        	要执行的URL 数组
	 *        	$urls=[['url'=>'www.ooyyee.com','params'=>[]]]
	 * @param number $usleep
	 *        	每次间隔时间 微秒
	 * @return array
	 */
	public static function mutilPost(array $urls, $usleep = 10000) {
		return self::mutilRequest ( $urls, 'post', $usleep );
	}
}

?>