<?php
namespace org\weixin;

use common\exception\WeixinException;
use common\exception\WeixinNoAccessTokenException;
use think\facade\Cache;
use org\tools\HttpTool;
use org\weixin\WeixinResponse;

abstract class Weixin
{

    protected $wechat;
    protected $appid;
    protected $secret;
    protected $_isWxMsgCrypt=false;
    protected $_wxBizMsgCrypt=null;
    protected $_wxRequestData=[];

    private static $instance = null;

    protected function __construct($wechat)
    {
        $this->wechat = $wechat;
        $this->appid=$wechat['appid'];
        $this->secret=$wechat['secret'];
        if (! $this->appid) {
            throw new \Exception('缺少appid参数', 41002);
        }
        if (!$this->secret) {
            throw new \Exception('缺少secret参数', 41004);
        }
    }
    
    
    

    /**
	 * @return the $appid
	 */
	public function getAppid() {
		return $this->appid;
	}

	/**
	 * @return the $secret
	 */
	public function getSecret() {
		return $this->secret;
	}

	/**
	 * @param field_type $appid
	 */
	public function setAppid($appid) {
		$this->appid = $appid;
	}

	/**
	 * @param field_type $secret
	 */
	public function setSecret($secret) {
		$this->secret = $secret;
	}

	/**
     * 获取微信实例
     *
     * @param string $cid            
     * @param string $appid            
     * @param string $secret            
     * @throws \Exception
     * @return ServiceWeixin
     */
    public static function getInstance($wechat)
    {
    	if (self::$instance == null) {
    		self::$instance = new ServiceWeixin($wechat);
    	}
    	return self::$instance;
    }

    /**
     * 获取Weixin access_token
     * 成功返回string access_token ,失败返回array 包含错误码
     *
     * @param boolean $refresh
     *            是否刷新
     * @throws WeixinException
     * @return string|array
     */
    public abstract function accessToken($refresh = false);

    /**
     * 微信上传文件
     * @param unknown $destFile
     * @param string $type
     * @param string $refresh
     * @return string|array
     */
    public abstract function uploadFile($destFile, $type = 'image', $refresh = false);
    
    
    
    
    /**
     * 自动添加access_token 参数<br/>
     * 如果获取access_token 成功 返回带access_token 参数的URL <br/>如果不成功 抛出异常
     *
     * @param string $url            
     * @param boolean $refreshToken            
     * @throws WeixinNoAccessTokenException|WeixinException
     * @return string 返回带access_token 参数的URL
     */
    protected function processAccessToken($url, $refreshToken, $extra = array())
    {
        $urlset = parse_url($url);
        $params = array();
        if (! empty($urlset['query'])) {
            parse_str($urlset['query'], $params);
        }
        if (empty($urlset['path'])) {
            $urlset['path'] = '/';
        }
        $access_token = $this->accessToken($refreshToken);
        if ($access_token && is_string($access_token)) {
            $params['access_token'] = $access_token;
            $params = array_merge($params, $extra);
            $args = http_build_query($params);
            $curl_url = $urlset['scheme'] . '://' . $urlset['host'] . $urlset['path'] . '?' . $args;
            return $curl_url;
        } else 
            if ($access_token && is_array($access_token)) {
                throw new \Exception($access_token['errmsg'], isset($access_token['errcode']) ? $access_token['errcode'] : '0');
            } else {
                throw new \Exception('微信TOKEN获取失败');
            }
    }

    /**
     * 执行Weixin Api GET 方式 ，不用带access_token 参数<br/>
     * 如果获取access_token 失败 ，抛出异常
     *
     * @param string $url            
     * @param boolean $refreshToken
     *            是否刷新access_token
     * @throws WeixinNoAccessTokenException|WeixinException
     * @return 返回执行结果
     */
    public function getData($url, $refreshToken = false)
    {
    	if(is_string($url)){
	    	$curl = $this->processAccessToken($url, $refreshToken);
	        $result = HttpTool::get($curl);
	        $result = @json_decode($result['content'], true);
	        if (isset($result['errcode']) && ($result['errcode'] == '42001' || $result['errcode'] == '40001')) {
	            return $this->getData($url, true);
	        } else {
	            return $result;
	        }
    	}elseif(is_array($url)){
    		foreach ($url as $key=>$urlInfo){
    			$curl = $this->processAccessToken($urlInfo['url'], $refreshToken);
    			$url[$key]['url']=$curl;
    		}
    		$results=HttpTool::mutilGet($url);
    		foreach ($results as $key=>$result){
    			$results[$key]['result']=@json_decode($result['result'],true);
    		}
    		return $results;
    	}
    }

    /**
     * 执行Weixin Api POST 方式 ，不用带access_token 参数<br/>
     * 如果获取access_token 失败 ，抛出异常
     *
     * @param string $url            
     * @param string $params            
     * @param boolean $refreshToken
     *            是否刷新access_token
     * @throws WeixinNoAccessTokenException|WeixinException
     * @return 返回执行结果
     */
    public function postData($url, $params=array(), $refreshToken = false)
    {
    	if(is_string($url)){
	        $curl = $this->processAccessToken($url, $refreshToken);
	        $result = HttpTool::post($curl, $params);
	        $result = @json_decode($result['content'], true);
	        if (isset($result['errcode']) && ($result['errcode'] == '42001' || $result['errcode'] == '40001')) {
	            return $this->postData($url, $params, true);
	        } else {
	            return $result;
	        }
    	}elseif(is_array($url)){
    		foreach ($url as $key=>$urlInfo){
	    		$curl = $this->processAccessToken($urlInfo['url'], $refreshToken);
	    		$url[$key]['url']=$curl;
    		}
    		$results=HttpTool::mutilPost($url);
    		foreach ($results as $key=>$result){
    			$results[$key]['result']=@json_decode($result['result'],true);
    		}
    		return $results;
    	}
    }

    private function _oauth($url, $scope, $state,$log=false)
    {
        $scope = $scope ?  : 'snsapi_base'; // snsapi_base
        session('weixin_oauth_state', $state);
        if($log){
            $authorize = array(
                'redirect_uri' => $url,
                'scope' => $scope,
                'state' => $state,
            	'appid' => $this->appid,
                'create_time' => time(),
            );
            $id = db('authorize')->insertGetId($authorize);
            $authorize['id'] = $id;
            cache('__OUATH2_AUTHORIZE_' . $id, $authorize, 20);
            return $id;
        }
        return 0;
    }

    /**
     * Weixin OAuth 验证 ，跳转到统一地址 然后再跳回来
     *
     * @param string $url            
     * @param string $scope            
     * @param string $state            
     */
    public function baseOauth($url, $state, $scope = 'snsapi_base')
    {
        $id = $this->_oauth($url, $scope, $state,true);
        $oauth_url =HttpTool::getDomain() . "/oauth2/$id";
        $redirect_uri = urlencode($oauth_url);
        HttpTool::redirect($oauth_url);
    }

    /**
     * Weixin OAuth 验证 ，跳转到URL
     *
     * @param string $url            
     * @param string $scope            
     * @param string $state            
     */
    public function oauth($url, $state, $scope = 'snsapi_base')
    {
    	$id = $this->_oauth($url, $scope, $state,true);
    	$redirect_uri = urlencode($url);
        $oauth_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->appid}&redirect_uri=$redirect_uri&response_type=code&scope=$scope&state=$state#wechat_redirect";
        return redirect($oauth_url);
    }
    /**
     * 获取oauth结果
     *
     * @return array 包含openid
     */
    public abstract function oauthResult();
    public abstract function ticket();
    public abstract function parse();
    public abstract function wxpay($prepay_id,$partnerKey);
    public function response($content,$type='text'){
        return WeixinResponse::response($this->_wxBizMsgCrypt, $this->_wxRequestData, $content,$type,$this->_isWxMsgCrypt);
    }
}

?>