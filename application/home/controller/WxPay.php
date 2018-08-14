<?php
namespace app\home\controller;

use think\Controller;
use think\Log;
use org\weixin\ProductNotify;
use org\tools\SystemTool;
use org\weixin\ChatNotify;

class WxPay extends Controller
{
	public function notify(){
		try{
			$config = SystemTool::getWeixinPayInfo();
			$notify = new ProductNotify($config);
			$notify->Handle ( false );
		}catch (\Exception $e){
			Log::error(array(
					'message' => $e->getMessage(),
					'file' => $e->getFile(),
					'line' => $e->getLine(),
					'code' => $e->getCode(),
			));
		}
	}
	
	public function chatNotify(){
		try{
			$config = SystemTool::getWeixinPayInfo();
			$notify = new ChatNotify($config);
			$notify->Handle ( false );
		}catch (\Exception $e){
			Log::error(json(array(
					'message' => $e->getMessage(),
					'file' => $e->getFile(),
					'line' => $e->getLine(),
					'code' => $e->getCode(),
			)));
		}
	}
}
