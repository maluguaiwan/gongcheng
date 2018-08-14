<?php
use think\Request;
use think\Db;
use think\Image;
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 应用公共文件
/** 根据附件表的id返回url地址
 * @param $id
 * @return bool|mixed|string
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function get_url($id)
{
    $domain = \think\facade\Request::domain();
    if ($id) {
        $getAttachment = (new \app\common\model\Attachment())->where(['id' => $id])->find();
        if(empty($getAttachment)) {
            //无资源
            return $domain.'/static/common/images/andphp_bg_null.png';
        }
        if($getAttachment['status'] === 0) {
            //待审核
            return $domain.'/static/common/images/andphp_bg_shenhe.png';
        }elseif($getAttachment['status'] === 1) {
            //审核通过
            if($getAttachment['location']==0){

                return $domain.'/'.$getAttachment['savepath'];
            }
            return $getAttachment['savepath'];
        }else {
            //不通过
            return $domain.'/static/common/images/andphp_bg_jujue.png';
        }
    }
    return false;
}

/** 密码加密方式
 * @param $password
 * @param $salt
 * @return string
 */
function passwordMD5($password,$salt)
{
    return md5(md5($password) . md5($salt));
}

/** 获取a-z,A-Z,0-9的随机字符串
 * @param $len
 * @return string
 */
function getRandStr($len) {
    $chars = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','0','1','2','3','4','5','6','7','8','9');
    $charsLen = count($chars) - 1;
    shuffle($chars);
    $output = '';
    for ($i = 0; $i < $len; $i++) {
        $output .= $chars[mt_rand(0, $charsLen)];
    }
    return $output;
}

/** 根据路由模型/控制器/方法 获取系统配置标题及描述
 * @param $module
 * @param $controller
 * @param $function
 * @return null|string
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function getTitle($module,$controller,$function)
{
    $info = (new \app\common\model\AuthRule())->where(['name'=>$module.'/'.$controller.'/'.$function])->find();
    if(empty($info)){
        return null;
    }
    //return  $info['title'].'-'.$info['description'];
    return  $info['title'].' '.$info['description'];
}

/** 根据路由模型/控制器/方法 获取系统配置标题及描述
 * @param $module
 * @param $controller
 * @param $function
 * @return mixed|null
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function getTitleOne($module,$controller,$function)
{
    $info = (new \app\common\model\AuthRule())->where(['name'=>$module.'/'.$controller.'/'.$function])->find();
    if(empty($info)){
        return null;
    }
    //return  $info['title'].'-'.$info['description'];
    return  $info['title'];
}

/** 根据目录名查询该目录下的所有文件夹名
 * @param $dir
 * @return array|bool
 */
function getNextDir($dir){
    $reInfo=[];
    if (is_dir($dir)){//如果是文件夹，遍历文件
        $arr = scandir($dir);
        foreach ($arr as $k=>$v){
            if ($v != '.' && $v != '..'){
                if (is_dir($dir."\\".$v)){
                    $reInfo[$k-1]['dir']=$v;
                }
            }
        }
        return $reInfo;
    }else{
        return false;
    }
}

/** 删除目录下所有文件
 * @param $dirName
 * @return bool
 */
function removeDir($dirName)
{
    if(! is_dir($dirName))
    {
        return false;
    }
    $handle = @opendir($dirName);
    while(($file = @readdir($handle)) !== false)
    {
        if($file != '.' && $file != '..')
        {
            $dir = $dirName . '/' . $file;
            is_dir($dir) ? removeDir($dir) : @unlink($dir);
        }
    }
    closedir($handle);

    return rmdir($dirName) ;
}
/*
 * ajaxReturn返回json数据
 */
function ajaxReturn($code,$msg='操作成功',$url='',$data=array(array('name'=>'paco','url'=>'yikaiba.com')),$render=true){
    $tmp['code']=$code;
    $tmp['msg']=$msg;
    $tmp['url']=$url;
    $tmp['data']=$data;
    $tmp['render']=$render;
    return json($tmp);
}
/*
 * 壹凯default 默认
 */
function yk_default($name,$default=''){
    if (empty($name)){
        return $default;
    }
    return $name;
}
/*
 * 获取订单状态
 */
function get_order_status($status){
    $check_arr=array(0,1,2,3,4);
    if (!in_array($status,$check_arr)){
        return '参数错误';
    }
    $arr=array(
        0=>'未付款',
        1=>'已付款',
        2=>'已发货',
        3=>'待评价',
        4=>'已完成'
    );
    return $arr[$status];
}
function get_reward_type_zh(){
    $tmp=array(
        0=>'注册送优惠券',
        1=>'购药',
        2=>'咨询',
        3=>'推荐医生',
        4=>'系统赠送优惠券',
    );
    return $tmp;
}
/**
 * 微信支付回调
 * type类型：本方法只有 1 、2
 * 0注册送优惠券
 * 1订单
 * 2咨询
 * 3人头费
 * 4系统赠送优惠券
 *
 *
 * type=1 或  type=2
 * 1、查询订单用户 是否有上级  member 关联 member_level
 *         a、没有上级，不做操作
 *         b、有上级 判断用户推荐等级
 *              （1）普通推荐商  普通（购药/咨询）比率给上级提成
 *              （2）Vip推荐商  Vip（购药/咨询）比率给上级提成
 *                 提成插入member_reward 表中
 *                 member_id 用户id
 *                 type 1订单2咨询
 *                 from_id 来源id （订单id / 咨询订单id）
 *                 reward_type 奖励类型 1优惠券，2现金  | 默认2
 *                 coupon_id 优惠券id
 *                 money 金额
 *                 update_time
 *                 create_time
 * 2、提成插入成功后 用户表member 余额和总额 同时增加
 */
function reward($type,$from_id=0){
    // 启动事务
    Db::startTrans();
    switch ($type){
        case 0://注册送优惠券
            $site_info=get_site_config('site');
            if ($site_info['if_reg_send']==1){//系统开启注册送优惠券功能
                //检查重复
                if (check_reward('',$type,$from_id)==false){
                    return ajaxReturn(0,'不能重复插入');
                }
                $data_reward=array(
                    'member_id'=>$from_id,//用户id
                    'type'=>$type,
                    'from_id'=>0,
                    'reward_type'=>1,
                    'coupon_id'=>1,
                    'money'=>0,//提成
                    'update_time'=>time(),
                    'create_time'=>time()
                );
                $rs=Db::name('member_reward')->insert($data_reward);
                if ($rs>0){
                    $data_member_coupon=array(
                        'coupon_id'=>1,
                        'member_id'=>$from_id,
                        'create_time'=>time()
                    );
                    $rs=Db::name('member_coupon')->insert($data_member_coupon);
                    if ($rs>0){
                        Db::commit();
                        return ajaxReturn(200,'注册发送优惠券成功');
                    }else{
                        Db::rollback();
                        return ajaxReturn(0,'失败');
                    }
                }else{
                    Db::rollback();
                    return ajaxReturn(0,'失败');
                }
            }
            break;
        case 1://购药
            if (empty($from_id)){
                return ajaxReturn(0,'参数错误');
            }
            $info=Db::name('order')->alias('A')
                ->join('member B','A.member_id=B.id')
                ->field('A.id,A.order_number,A.member_id,A.pay_amount,B.parent_id')
                ->where('A.id="'.$from_id.'" and A.status=1')
                ->find();
            //检查重复
            if (check_reward($info['parent_id'],$type,$from_id)==false){
                return ajaxReturn(0,'不能重复插入');
            }
            //如果有该订单用户有上级分销
            if (!empty($info['parent_id'])){
                $parent_info=Db::name('member')->alias('A')
                    ->join('member_level B','A.level_id=B.id')
                    ->field('A.amount,A.total_amount,B.name,B.protect')
                    ->where('A.id="'.$info['parent_id'].'"')
                    ->find();
                $money=round(floatval($info['pay_amount'])*floatval($parent_info['protect']/100),2);
                $data_reward=array(
                    'member_id'=>$info['parent_id'],
                    'type'=>$type,
                    'from_id'=>$from_id,
                    'reward_type'=>2,
                    'coupon_id'=>0,
                    'money'=>$money,//提成
                    'update_time'=>time(),
                    'create_time'=>time()
                );
                $rs=Db::name('member_reward')->insert($data_reward);
                if ($rs>0){
                    $data_member=array(
                        'amount'=>floatval($parent_info['amount'])+$money,
                        'total_amount'=>floatval($parent_info['total_amount'])+$money
                    );
                    $rs=Db::name('member')->where('id="'.$info['parent_id'].'"')->update($data_member);
                    if ($rs>0){
                        Db::commit();
                        return ajaxReturn(200,'购药提成加入成功');
                    }else{
                        Db::rollback();
                        return ajaxReturn(0,'失败');
                    }
                }else{
                    Db::rollback();
                    return ajaxReturn(0,'失败');
                }
            }
            break;
        case 2://咨询
            if (empty($from_id)){
                return ajaxReturn(0,'参数错误');
            }
            $info=Db::name('doctor_pay')->alias('A')
                ->join('member B','A.member_id=B.id')
                ->field('A.id,A.member_id,A.total_amount,B.parent_id')
                ->where('A.id="'.$from_id.'" and A.status=1')
                ->find();
            //检查重复
            if (check_reward($info['parent_id'],$type,$from_id)==false){
                return ajaxReturn(0,'不能重复插入');
            }
            //如果有该订单用户有上级分销
            if (!empty($info['parent_id'])){
                $parent_info=Db::name('member')->alias('A')
                    ->join('member_level B','A.level_id=B.id')
                    ->field('A.amount,A.total_amount,B.name,B.advice')
                    ->where('A.id="'.$info['parent_id'].'"')
                    ->find();
                $money=round(floatval($info['total_amount'])*floatval($parent_info['advice']/100),2);
                $data_reward=array(
                    'member_id'=>$info['parent_id'],
                    'type'=>$type,
                    'from_id'=>$from_id,
                    'reward_type'=>2,
                    'coupon_id'=>0,
                    'money'=>$money,//提成
                    'update_time'=>time(),
                    'create_time'=>time()
                );
                $rs=Db::name('member_reward')->insert($data_reward);
                if ($rs>0){
                    $data_member=array(
                        'amount'=>floatval($parent_info['amount'])+$money,
                        'total_amount'=>floatval($parent_info['total_amount'])+$money
                    );
                    $rs=Db::name('member')->where('id="'.$info['parent_id'].'"')->update($data_member);
                    if ($rs>0){
                        Db::commit();
                        return ajaxReturn(200,'咨询提成加入成功');
                    }else{
                        Db::rollback();
                        return ajaxReturn(0,'失败');
                    }
                }else{
                    Db::rollback();
                    return ajaxReturn(0,'失败');
                }
            }
            break;
        case 3://人头费
            $info=Db::name('Member')->field('id,type,parent_id')->where('id="'.$from_id.'"')->find();
            //检查重复
            if (check_reward($info['parent_id'],$type,$from_id)==false){
                return ajaxReturn(0,'不能重复插入');
            }
            if ($info['type']==2 and $info['parent_id']>0){//注册的是医生给人头费
                $parent_info=Db::name('Member')->alias('A')
                    ->join('member_level B','A.level_id=B.id')
                    ->field('A.id,A.type,A.amount,A.total_amount,B.name,B.head')
                    ->where('B.id="'.$info['parent_id'].'"')
                    ->find();
                $money=round(floatval($parent_info['head']),2);
                $data_reward=array(
                    'member_id'=>$info['parent_id'],
                    'type'=>$type,
                    'from_id'=>$from_id,
                    'reward_type'=>2,
                    'coupon_id'=>0,
                    'money'=>$money,//提成
                    'update_time'=>time(),
                    'create_time'=>time()
                );
                $rs=Db::name('member_reward')->insert($data_reward);
                if ($rs>0){
                    $data_member=array(
                        'amount'=>floatval($parent_info['amount'])+$money,
                        'total_amount'=>floatval($parent_info['total_amount'])+$money
                    );
                    $rs=Db::name('member')->where('id="'.$info['parent_id'].'"')->update($data_member);
                    if ($rs>0){
                        Db::commit();
                        return ajaxReturn(200,'人头费加入成功');
                    }else{
                        Db::rollback();
                        return ajaxReturn(0,'失败');
                    }
                }else{
                    Db::rollback();
                    return ajaxReturn(0,'失败');
                }
            }
            break;
        case 4:
            break;
        default:
            return ajaxReturn(0,'参数错误');
    }
}
/**
 * 检查重复插入
 */
function check_reward($member_id,$type,$from_id){
    if ($type==0){
        $check=Db::name('member_reward')->where('member_id="'.$from_id.'" and type="'.$type.'" and coupon_id=1')->count();
        if ($check>0){
            return false;
        }else{
            return true;
        }
    }elseif ($type==1 or $type==2 or $type==3){
        $check=Db::name('member_reward')->where('member_id="'.$member_id.'" and type="'.$type.'" and from_id="'.$from_id.'"')->count();
        if ($check>0){
            return false;
        }else{
            return true;
        }
    }elseif ($type==4){

    }

}

/**
 * 获取网站配置
 */

function get_site_config($group='site'){

    $list=Db::name('system_config')->where('group',$group)->column('value','vari');

    return $list;

}

//获取缩略图
function get_thumb_img($img,$width='650'){
    if (empty($img)||!file_exists('.'.$img)){
        return '';
    }
    $height=$width;
    $img_info=explode('/',$img);
    $img_path=$img_info[1].DS.$img_info[2].DS.$img_info[3].DS.$img_info[4];
    if (!is_dir('./'.$img_path)){
        mkdir($img_path, 0755, true);
    }
    $thumb_name=$img_info[5];
    if (file_exists(ROOT_PATH.$img_path.'/thumb_'.$width.'_'.$thumb_name)){
        return '/'.$img_path.'/thumb_'.$width.'_'.$thumb_name;
    }else{
        try{
            $image = Image::open(ROOT_PATH.$img);
            $image->thumb($width, $height)->save('./'.$img_path.'/thumb_'.$width.'_'.$thumb_name);
            return '/'.$img_path.'/thumb_'.$width.'_'.$thumb_name;
        }catch(\Exception $e){
            return $img;
        }

    }
}