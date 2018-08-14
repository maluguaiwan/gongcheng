<?php
namespace app\home\controller;

use app\common\controller\IndexBase;

class Ticket extends IndexBase
{
	public function index(){
		return $this->getShareTicket();
	}
}