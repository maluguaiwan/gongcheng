<?php
namespace app\home\controller;

use app\common\controller\IndexBase;
use GatewayWorker\Lib\Gateway;

class Message extends IndexBase
{
	public function onlineCheck(){
		$id = $this->request->param('id');
		if(!$id){
			return 0;
		}
		$status = Gateway::isUidOnline($id);
		return $status;
	}
}