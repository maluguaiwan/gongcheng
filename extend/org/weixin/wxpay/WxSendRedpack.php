<?php

namespace org\weixin\wxpay;

class WxSendRedpack extends WxPayDataBase{

	/**
	 * 设置微信分配的公众账号ID
	 * @param string $value
	 **/
	public function SetAppid($value)
	{
		$this->values['wxappid'] = $value;
	}
	
	/**
	 * 获取微信分配的公众账号ID的值
	 * @return 值
	 **/
	public function GetAppid()
	{
		return $this->values['wxappid'];
	}
	/**
	 * 判断微信分配的公众账号ID是否存在
	 * @return true 或 false
	 **/
	public function IsAppidSet()
	{
		return array_key_exists('wxappid', $this->values);
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
	
	//提供方名称
	public function SetNick_name($value){
		$this->values['nick_name'] = $value;
	}
	public function GetNick_name(){
		return $this->values['nick_name'];
	}
	public function IsNick_nameSet(){
		return array_key_exists('nick_name', $this->values);
	}
	
	//商户名称
	public function SetSend_name($value){
		$this->values['send_name'] = $value;
	}
	public function GetSend_name(){
		return $this->values['send_name'];
	}
	public function IsSend_nameSet(){
		return array_key_exists('send_name', $this->values);
	}
	//用户openid
	public function SetRe_openid($value){
		$this->values['re_openid'] = $value;
	}
	public function GetRe_openid(){
		return $this->values['re_openid'];
	}
	public function IsRe_openidSet(){
		return array_key_exists('re_openid', $this->values);
	}
	//金额
	public function SetTotal_amount($value){
		$this->values['total_amount'] = $value;
	}
	public function GetTotal_amount(){
		return $this->values['total_amount'];
	}
	public function IsTotal_amountSet(){
		return array_key_exists('total_amount', $this->values);
	}
	
	public function SetMax_value($value){
		$this->values['max_value'] = $value;
		$this->values['min_value'] = $value;
	}
	public function IsMax_valueSet(){
		return array_key_exists('max_value', $this->values);
	}
	
	public function SetTotal_num($value){
		$this->values['total_num'] = $value;
	}
	public function IsTotal_numSet(){
		return array_key_exists('total_num', $this->values);
	}
	//红包祝福语
	public function SetWishing($value){
		$this->values['wishing'] = $value;
	}
	public function GetWishing(){
		return $this->values['wishing'];
	}
	public function IsWishingSet(){
		return array_key_exists('wishing', $this->values);
	}
	
	//活动名称
	public function SetAct_name($value){
		$this->values['act_name'] = $value;
	}
	public function GetAct_name(){
		return $this->values['act_name'];
	}
	public function IsAct_nameSet(){
		return array_key_exists('act_name', $this->values);
	}
	
	/*备注*/
	public function SetRemark($value){
		$this->values['remark'] = $value;
	}
	public function GetRemark(){
		return $this->values['remark'];
	}
	public function IsRemarkSet(){
		return array_key_exists('remark', $this->values);
	}
	
	
	public function SetClient_ip($value){
		$this->values['client_ip'] = $value;
	}
	
	public function GetClient_ip(){
		return $this->values['client_ip'];
	}
	
	
}

?>