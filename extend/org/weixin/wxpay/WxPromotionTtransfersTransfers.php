<?php

namespace org\weixin\wxpay;

class WxPromotionTtransfersTransfers extends WxPayDataBase{

	/**
	 * 设置微信分配的公众账号ID
	 * @param string $value
	 **/
	public function SetAppid($value)
	{
		$this->values['mch_appid'] = $value;
	}
	
	/**
	 * 获取微信分配的公众账号ID的值
	 * @return 值
	 **/
	public function GetAppid()
	{
		return $this->values['mch_appid'];
	}
	/**
	 * 判断微信分配的公众账号ID是否存在
	 * @return true 或 false
	 **/
	public function IsAppidSet()
	{
		return array_key_exists('mch_appid', $this->values);
	}
	
	
	/**
	 * 设置微信支付分配的商户号
	 * @param string $value
	 **/
	public function SetMch_id($value)
	{
		$this->values['mchid'] = $value;
	}
	/**
	 * 获取微信支付分配的商户号的值
	 * @return 值
	 **/
	public function GetMch_id()
	{
		return $this->values['mchid'];
	}
	/**
	 * 判断微信支付分配的商户号是否存在
	 * @return true 或 false
	 **/
	public function IsMch_idSet()
	{
		return array_key_exists('mchid', $this->values);
	}
	
	/**
	 * 设置随机字符串
	 * @param string $value
	 **/
	public function SetNonce_str($value)
	{
		$this->values['nonce_str'] = $value;
	}
	/**
	 * 获取随机字符串的值
	 * @return 值
	 **/
	public function GetNonce_str()
	{
		return $this->values['nonce_str'];
	}
	/**
	 * 判断随机字符串是否存在
	 * @return true 或 false
	 **/
	public function IsNonce_strSet()
	{
		return array_key_exists('nonce_str', $this->values);
	}
	
	/**
	 * 设置商户订单号
	 * @param string $value
	 **/
	public function SetPartner_trade_no($value)
	{
		$this->values['partner_trade_no'] = $value;
	}
	/**
	 * 获取商户订单号的值
	 * @return 值
	 **/
	public function GetPartner_trade_no()
	{
		return $this->values['partner_trade_no'];
	}
	/**
	 * 判断商户订单号是否存在
	 * @return true 或 false
	 **/
	public function IsPartner_trade_noSet()
	{
		return array_key_exists('partner_trade_no', $this->values);
	}
	
	//用户openid
	public function SetOpenid($value){
		$this->values['openid'] = $value;
	}
	public function GetOpenid(){
		return $this->values['openid'];
	}
	public function IsOpenidSet(){
		return array_key_exists('openid', $this->values);
	}
	
	//金额
	public function SetAmount($value){
		$this->values['amount'] = $value;
	}
	public function GetAmount(){
		return $this->values['amount'];
	}
	public function IsAmountSet(){
		return array_key_exists('amount', $this->values);
	}
	
	//校验用户姓名选项
	public function SetCheck_name($value){
		$this->values['check_name'] = $value;
	}
	public function GetCheck_name(){
		return $this->values['check_name'];
	}
	public function IsCheck_nameSet(){
		return in_array($this->values['check_name'],array(self::CHECK_NAME_OPTION_NOCHECK,'FORCE_CHECK','OPTION_CHECK'));
	}
	
	const CHECK_NAME_OPTION_NOCHECK = 'NO_CHECK';
	
	/*备注*/
	public function SetDesc($value){
		$this->values['desc'] = $value;
	}
	public function GetDesc(){
		return $this->values['desc'];
	}
	public function IsDescSet(){
		return array_key_exists('desc', $this->values);
	}
	
	public function SetClient_ip($value){
		$this->values['spbill_create_ip'] = $value;
	}
	
	public function GetClient_ip(){
		return $this->values['spbill_create_ip'];
	}
	
}

?>