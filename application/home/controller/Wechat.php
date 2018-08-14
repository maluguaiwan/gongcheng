<?php
namespace app\home\controller;

use think\Controller;
use think\facade\Log;

class Wechat extends Controller
{
    public function index()
    {
    	try {
    	    Log::write("wechat:".$this->request->url(true));
    		$result = $this->data = weixin()->parse();
    		if ($result ['status'] == 1) {
    			list ( $content, $type ) = $this->reply ( $result ['data'] );
    			if ($content === false) {
    				return 'success';
    			}
    			$response = weixin()->response ( $content, $type );
    			return $response;
    		} else {
    			return $result ['errmsg'];
    		}
    	} catch ( \Exception $e ) {
    		Log::error( array (
    				'message' => $e->getMessage (),
    				'file' => $e->getFile (),
    				'line' => $e->getLine () ));
    	}
    }
}
