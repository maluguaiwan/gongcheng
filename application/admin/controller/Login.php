<?php
namespace app\admin\controller;
use app\common\controller\AppBase;
use app\common\model\AdminUser as AdminUserModel;
use think\facade\Session;
use app\admin\validate\AdminUser as AdminUserValidate;

class Login extends AppBase
{
    /** 渲染登录页面
     * @return mixed
     */
	public function index(){
		if(Session::has('adminUser') == true) {
			$this->redirect('admin/index/index');
		}
		return $this->fetch();
	}

    /** 登录操作
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
	public function login_username(){
		$model = new AdminUserModel();
		if (!$this->request->isAjax()) {
			return ajaxReturn(0,'不是一个正确的请求方式');
		}
		$post = $this->request->post();
		//验证部分数据合法性
		(new AdminUserValidate())->goCheck($post);
		if (!captcha_check(input('post.captcha'))) {
            return ajaxReturn(0,'验证码错误!');
		}
		$name = $model->where('username',$post['username'])->find();
		if(empty($name)) {
			//不存在该用户名
            return ajaxReturn(0,'用户名不存在!');
		} else {
			//验证密码
			$post['password'] = passwordMD5($post['password'],$name['salt']);
			if($name['password'] != $post['password']) {
                return ajaxReturn(0,'密码错误!');
			} elseif ($name['status'] != 1) {
                return ajaxReturn(0,'当前用户已禁用!');
			}else{
				$login_ip =  $this->request->ip();
				$login_time = time();
				$logLogin['user_id']=$name['id'];
				$logLogin['username']=$name['username'];
				$url='http://ip.taobao.com/service/getIpInfo.php?ip='.$login_ip;
				$result = file_get_contents($url);
				$result = json_decode($result,true);
				$logLogin['city']=$result['data']['city'];
				$logLogin['login_ip']=$login_ip;
				$logLogin['login_time']=$login_time;
				Session::set("adminUser",$name); //保存新的,最长为2小时
				//记录登录时间和ip
				$model->where('id',$name['id'])->update(['last_login_ip' => $login_ip,'last_login_time' => $login_time]);
				$this->add_log($name['id'],$name['username'],'登录于'.date('Y-m-d H:i:s',$login_time));
                return ajaxReturn(200,'登录成功,正在跳转...','/admin/Index/index');
			}
		}
	}

    /** 退出登录
     * @return string
     */
	public function logout(){
		Session::delete('adminUser');
		if(empty(Session::get('adminUser'))) {
			return json_success('注销成功！正在跳转...','/admin/login/index');
		}
		return json_error('注销失败！');
	}
}