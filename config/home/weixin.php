<?php
return [
		'appid'   => 'wx3237e0ec3a6be08f',    // 微信appid	
		'secret'  => 'bc9abc9b6df8b3bcd5419cdecdd89761',    // 微信seacret
		'token'   => 'health',
        'aeskey'  => 'McXPyWMe4hs5jixsfFBH36mI4DqD6yilhh0NodsFbG9',
		'share_info' => [
				'title' => '注册会员，享受更多好礼~',
				'desc'  => '好大夫在线商城，注册会员享受更多好礼',
				'image' => '/static/home/images/logo.png'
		],
		'company_name' => '测试公司',
		'wxpay'   => [
				'is_wxpay'   => 1,
				'partnerId'  => '1500240122',  //商户号
				'partnerKey' => 'NvoZmjJrvw1rawA025zrJqFUfTGcyF10',  //支付密钥
				'cert_file'  => 'cert/apiclient_cert.pem',  //微信支付证书 apiclient_cert.pem
				'sslkey_path'=> 'cert/apiclient_key.pem',  //微信支付密钥证书 apiclient_key.pem
				'pay_path'   => '/wxpay',      //微信支付目录
		]
];