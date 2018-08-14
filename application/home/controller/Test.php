<?php
namespace app\home\controller;

use think\Controller;
use org\tools\OAuthTool;

class Test extends Controller
{
	public function index(){
		$openid = $this->request->param('openid');
		OAuthTool::member($openid?$openid:'oBqPg0wY6qbQ9EXhiXR0bTLB6VjM');
		
		echo '<br>';
		echo session('is_pay_in_time_13_1');
		var_dump(session('is_pay_in_time_13_1'));
		
		return "test";
		
		/*
		$res = SmsTool::send('15904115591', '1142');
		var_export($res);
		exit('success');
		*/
	}
	
	public function cache(){
		cache('weixin_oauth',null);
		cookie ( "weixin_oauth",null);
		exit('cache success');
	}
}
