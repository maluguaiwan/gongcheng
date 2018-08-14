<?php
namespace app\push\controller;

use GatewayWorker\Lib\Gateway;
use app\common\model\MemberClient;
use app\common\model\Member;
use org\tools\OAuthTool;
use app\common\model\MessageHistory;
use app\common\model\DoctorPay;

/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events
{

    /**
     * 当客户端发来消息时触发
     * 
     * @param int $client_id
     *            连接id
     * @param mixed $data
     *            具体消息
     */
    public static function onMessage($client_id, $data)
    {
        if(!$data){
            return;
        }
        $message = json_decode($data, true);
        $message_type = $message['type'];
        switch ($message_type) {
        	case 'doctor_init':
        		$uid = $message['uid'];
        		$flag = MemberClient::insert(['uid'=>$uid,'client_id'=>$client_id,'create_time'=>time()],true);
        		if($flag){
        			Gateway::bindUid($client_id, $uid);
        		}else{
        			return;
        		}
        		$init_message = array(
        				'message_type' => 'init',
        				'id' => $uid,
        				'client_id' => $client_id
        		);
        		return Gateway::sendToClient($client_id, json_encode($init_message));
        		break;
            case 'init':
                $to_type = !empty($message['to_type'])?$message['to_type']:'doctor'; // 对象为医生或者会员
                $doctor_id = $message['doctor_id'];
                $member_id = $message['member_id'];
                $doctorInfo = Member::where(['id'=>$doctor_id])->find();
                $memberInfo = Member::where(['id'=>$member_id])->find();
                if(!$memberInfo){
                	$init_message = array(
                			'message_type' => 'init',
                			'status' => 'error',
                			'msg'  => '登录信息不存在,刷新重试',
                			'client_id' => $client_id
                	);
                	return Gateway::sendToClient($client_id, json_encode($init_message));
                }
                $uid = $to_type == 'doctor' ? $doctor_id : $member_id;
                $self_uid = $to_type == 'doctor' ? $member_id : $doctor_id;
                $is_online = Gateway::isUidOnline($uid);
                
                $is_client =DoctorPay::where(['doctor_id'=>$doctor_id,'member_id'=>$member_id,'status'=>1])->count();
               	
                echo $uid."\n\r";
                echo $member_id."\n\r";
                echo $client_id."\n\r";
                
                $flag = MemberClient::insert(['uid'=>$self_uid,'client_id'=>$client_id,'create_time'=>time()],true);
                if($flag){
                	Gateway::bindUid($client_id, $self_uid);
                }else{
                	return;
                }
                Gateway::sendToAll(json_encode(['message_type'=>'toupdate']));
                // 查询是否zaixian
                // 通知当前客户端初始化
                $init_message = array(
	                    'message_type' => 'init',
	                	'status' => 'success',
                		'is_online' => $is_online,
	                	'is_client'=>$is_client?1:0,
	                    'uid' => $uid,
	                	'client_id' => $client_id,
                		'self_online' => Gateway::isUidOnline($self_uid)
                );
                
                return Gateway::sendToClient($client_id, json_encode($init_message));
            case 'message':
                // 聊天消息
                $to_id = $message['data']['to_uid']; // 发送对象的ID
                $uid = $message['data']['self_id']; // 自己的ID
                $message_content = htmlspecialchars($message['data']['content']);
                $member = db('member')->find($uid);
                $chat_message = [
                    'message_type' => 'message',
                    'data' => [
                        'username' => $member['name'],
                        'id' => $uid,
                    	'content' => htmlspecialchars_decode($message_content),
                        'timestamp' => date('Y-m-d H:i')
                    ]
                ];
                
                // 保存历史信息
                $savedata = [
                    'uid'     => $uid,
                    'to_uid'  => $to_id,
                	'message' => htmlspecialchars_decode($message_content),
                	'status'  => 0,
                    'create_time' => time()
                ];
                db('message_history')->insert($savedata);
                
                return Gateway::sendToUid($to_id, json_encode($chat_message));
            case 'update':
            	$uid = $message['uid'];
            	$to_uid = $message['to_uid'];
            	$type = $message['to_type'];
            	
            	$is_online = Gateway::isUidOnline($to_uid);
            	$member_id = $type == 'doctor' ? $uid : $to_uid;
            	$doctor_id = $type == 'doctor' ? $to_uid : $uid;
            	$is_client =DoctorPay::where(['doctor_id'=>$doctor_id,'member_id'=>$member_id,'status'=>1])->count();
            	return Gateway::sendToUid($uid, json_encode(['message_type'=>'update','is_online'=>$is_online,'is_client' => $is_client]));
            	break;
            case 'history':
            	$uid    = $message['uid'];
            	$to_uid = $message['to_uid'];
            	$sql = "( uid = {$uid} AND to_uid = {$to_uid} ) OR ( uid = {$to_uid} AND to_uid = {$uid} )";
            	$history = MessageHistory::where($sql)->order('create_time','ASC')->limit(50)->select();
            	$historyData = $history->toArray();
            	foreach ($historyData as &$v){
            		$v['self'] = $v['uid'] == $uid ? 1 : 0;
            		$v['message'] = htmlspecialchars_decode($v['message']);
            	}
            	$return_message = [
            			'message_type'  => 'history',
            			'data' => $historyData
            	];
            	
            	
            	$res = Gateway::sendToClient($client_id, json_encode($return_message));
            	if($res){
            		MessageHistory::where('to_uid',$uid)->update(['status'=>1]);
            	}
            	break;
            case 'ping':
                return;
            default:
                echo "unknown message $data" . PHP_EOL;
        }
    }

    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     *
     * @param int $client_id
     *            连接id
     */
    public static function onConnect($client_id)
    {
        
    }

    /**
     * 当连接断开时触发的回调函数
     * 
     * @param
     *            $connection
     */
    public static function onClose($client_id)
    {
        
    }

    /**
     * 当客户端的连接上发生错误时触发
     * 
     * @param
     *            $connection
     * @param
     *            $code
     * @param
     *            $msg
     */
    public static function onError($client_id, $code, $msg)
    {
        echo "error $code $msg\n";
    }

    /**
     * 每个进程启动
     * 
     * @param
     *            $worker
     */
    public static function onWorkerStart($worker)
    {}
}