<?php

namespace org\weixin\wxpay;

class WxRedpackQuery extends WxPayDataBase{

	/**
	 * 设置微信分配的公众账号ID
	 * @param string $value
	 **/
	public function SetAppid($value)
	{
		$this->values['appid'] = $value;
	}
	
	/**
	 * 获取微信分配的公众账号ID的值
	 * @return 值
	 **/
	public function GetAppid()
	{
		return $this->values['appid'];
	}
	/**
	 * 判断微信分配的公众账号ID是否存在
	 * @return true 或 false
	 **/
	public function IsAppidSet()
	{
		return array_key_exists('appid', $this->values);
	}
	
	
	/**
	 * 设置微信支付分配的商户号
	 * @param string $value
	 **/
	public function SetMch_id($value)
	{
		$this->values['mch_id'] = $value;
	}
	/**
	 * 获取微信支付分配的商户号的值
	 * @return 值
	 **/
	public function GetMch_id()
	{
		return $this->values['mch_id'];
	}
	/**
	 * 判断微信支付分配的商户号是否存在
	 * @return true 或 false
	 **/
	public function IsMch_idSet()
	{
		return array_key_exists('mch_id', $this->values);
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
	public function SetMch_billno($value)
	{
		$this->values['mch_billno'] = $value;
	}
	/**
	 * 获取商户订单号的值
	 * @return 值
	 **/
	public function GetMch_billno()
	{
		return $this->values['mch_billno'];
	}
	/**
	 * 判断商户订单号是否存在
	 * @return true 或 false
	 **/
	public function IsMch_billnoSet()
	{
		return array_key_exists('mch_billno', $this->values);
	}
	
	public function SetBill_type($value){
		$this->values['bill_type']=$value;
	}
	public function GetBill_type(){
		return $this->values['bill_type'];
	}
	public function IsBill_typeSet(){
		return array_key_exists('bill_type', $this->values);
	}
	
}

?>