<?php
namespace org\weixin;
use think\facade\Cache;
use org\weixin\service\WXBizMsgCrypt;
use org\tools\HttpTool;
use think\facade\Log;

class ServiceWeixin extends Weixin
{
    protected function __construct( $wechat)
    {
        parent::__construct($wechat);
    }
    /**
     * (non-PHPdoc)
     * @see \common\weixin\Weixin::accessToken()
     */
    public function accessToken($refresh = false)
    {
        
        $CACHE_KEY = 'weixin_access_token_' . $this->appid;
        if ($refresh) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $this->appid . "&secret=" . $this->secret;
            $content = HttpTool::get($url);
            Log::write(json_encode($content));
            $result = @json_decode($content['content'], true);
            $access_token = $result['access_token'];
            $expires_in = (int) $result['expires_in'];
            if ($access_token) {
                Cache::set($CACHE_KEY, $access_token, $expires_in - 200);
                return $access_token;
            } else {
                return $result;
            }
        } else {
            $access_token = Cache::get($CACHE_KEY);
            if ($access_token) {
                return $access_token;
            } else {
                return self::accessToken(true);
            }
        }
    }
    public function oauthResult(){
        $request=request();
        $code=$request->get('code',null);
        $state=$request->get('state',null);
        if(!empty($code)&&$code!='error'&&$state==session('weixin_oauth_state')){
            Log::write('weixin_auto weixin_oauth_state:'.$code);
        	session('weixin_oauth_state',null);
            $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->appid}&secret={$this->secret}&code={$code}&grant_type=authorization_code";
            $result=HttpTool::get($url);
            $data=@json_decode($result['content'],true);
            $openid=!empty($data ['openid'])?$data ['openid']:'';
            Log::write('weixin_auto weixin_oauth_state openid:'.$openid);
            if ($openid) {
                if ($data['scope']=='snsapi_userinfo') { // 系统获取用户信息
                    $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$data['access_token']}&openid={$openid}&lang=zh_CN";
                    $userResult=HttpTool::get($url);
                    Log::write('weixin_auto weixin_oauth_state $userResult:'.json_encode($userResult));
                    return @json_decode($userResult['content'],true);
                }else{
                    return $data;
                }
            }else{
                return $data;
            }
        }else{
            return false;
        }
    }
    
    public function uploadFile($destFile,$type='image',$refresh=false){
        $url='https://api.weixin.qq.com/cgi-bin/media/upload';
        $curl=$this->processAccessToken($url, $refresh,array('type'=>$type));
        $result= HttpTool::upload($curl, array('media' => new \CURLFile($destFile)));
        $result = @json_decode ( $result, true );
        if (isset($result['errcode'])&&($result['errcode'] == '42001'||$result['errcode']=='40001'||$result['errcode']=='40014')) {
            return $this->uploadFile($destFile,$type,true);
        } else {
            return $result;
        }
    }
    public function ticket(){
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi";
        $result = $this->getData($url);
        return $result;
    }
    
    public function parse(){
    	 $request = request();
    	 $timeStamp = $request->param('timestamp');
    	 $nonce = $request->param('nonce');
    	 $ecshostr= $request->param('echostr');
    	 $signature = $request->param('signature');
    	 $crypt = new WXBizMsgCrypt($this->wechat['token'], $this->wechat['aeskey'], $this->wechat['appid']);
    	 $this->_wxBizMsgCrypt=$crypt;
    	 if ($ecshostr) {
    		$errCode = $crypt->checkSignature($timeStamp, $nonce, $ecshostr, $signature);
    		if ($errCode == 0) {
    			return array(
    					'status' => 0,
    					'errcode' => $errCode,
    					'errmsg' => $ecshostr
    			);
    		} else {
    			return array(
    					'status' => 0,
    					'errcode' => $errCode,
    					'errmsg' => $errCode
    			);
    		}
    	} else {
    		$xml = file_get_contents("php://input");
    		if($xml){
	    		$data = XML::xml2array($xml);
	    		if (isset($data['Encrypt'])) {
	    			$msg = '';
	    			$errCode = $crypt->decryptMsg($timeStamp, $nonce, $xml, $msg);
	    			if ($errCode == 0) {
	    				$data = XML::xml2array($msg);
	    			}
	    			$this->_isWxMsgCrypt = true;
	    		}
	    		$this->_wxRequestData = $data;
	    		return array(
	    				'status' => 1,
	    				'data' => $data
	    		);
    		}else{
    			return ['status'=>0,'errmsg'=>'empty data'];
    		}
    	}
    	 
    }
    
    public function wxpay($prepay_id,$partnerKey){
    	$data=[];
    	$data['appId']=$this->appid;
    	$data['timeStamp'] = time()."";
    	$data['nonceStr'] = WXPayTools::create_noncestr();
    	$data['package'] = "prepay_id={$prepay_id}";
    	$data['signType'] = "MD5";
    	$sign = WXPayTools::createSign($data, $partnerKey);
    	$data['paySign'] = $sign;
    	return $data;
    }
    
}

?>