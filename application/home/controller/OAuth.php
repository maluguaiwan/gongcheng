<?php
namespace app\home\controller;

use think\Controller;
use think\facade\Cache;
use org\tools\HttpTool;

class OAuth extends Controller
{
    public function auth()
    {
    	$id=request()->param('id');
    	$code=request()->param('code');
    	$state=request()->param('state');
    	$authorize=$this->__get_authorize($id);
    	if($authorize){
    		if($authorize['state']==$state && $code){
    		    db('authorize')->where(['id'=>$id])->update(['code'=>$code]);
    			$urlset = parse_url ( urldecode($authorize['redirect_uri']) );
    			if (empty ( $urlset ['path'] )) {
    				$urlset ['path'] = '/';
    			}
    			if (! empty ( $urlset ['query'] )) {
    				$urlset ['query'] = "?{$urlset['query']}&code=$code&state=$state";
    			}else{
    				$urlset ['query']="?code=$code&state=$state";
    			}
    			if (empty ( $urlset ['port'] )) {
    				$urlset ['port'] = '80';
    			}
    			$url=$urlset ['scheme'] . '://' . $urlset ['host'] . ($urlset ['port'] == '80' ? '' : ':' . $urlset ['port']) . $urlset ['path'] . $urlset ['query'];
    			HttpTool::redirect($url);
    		}else{
    			$this->error('非法访问本链接!O(∩_∩)O~');
    		}
    	}else{
    		$this->error('授权失败!O(∩_∩)O~');
    	}
    	return json(['test'=>1]);
    }
    
    public function __get_authorize($id){
    	$authorize=cache('__OUATH2_AUTHORIZE_'.$id);
    	if(!$authorize){
    		$authorize=db('authorize')->find($id);
    	}
    	return $authorize;
    }
}
