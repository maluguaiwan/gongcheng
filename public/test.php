<?php

$arr = 'a:17:{s:5:"appid";s:18:"wx3237e0ec3a6be08f";s:9:"bank_type";s:3:"CFT";s:8:"cash_fee";s:1:"1";s:8:"fee_type";s:3:"CNY";s:12:"is_subscribe";s:1:"Y";s:6:"mch_id";s:10:"1500240122";s:9:"nonce_str";s:32:"cs853pw4k5fczfea0fjenthkon2sklm5";s:6:"openid";s:28:"oBqPg0wY6qbQ9EXhiXR0bTLB6VjM";s:12:"out_trade_no";s:16:"C805833994851501";s:11:"result_code";s:7:"SUCCESS";s:11:"return_code";s:7:"SUCCESS";s:4:"sign";s:32:"2B86EA5EAD0EF709D37497E23FE5EFA0";s:8:"time_end";s:14:"20180805233643";s:9:"total_fee";s:1:"1";s:10:"trade_type";s:5:"JSAPI";s:14:"transaction_id";s:28:"4200000185201808059918886414";s:11:"create_time";i:1533483647;}';

$new = unserialize($arr);
var_export($new);