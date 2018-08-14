<?php
namespace app\admin\controller;
use app\common\controller\AdminBase;
use app\common\model\SystemConfig as SystemConfigModel;
use app\admin\validate\SystemConfig as SystemConfigValidate;
use think\facade\Cache;
use think\Request;

/** 系统配置控制器
 * Class SystemConfig
 * @package app\admin\controller
 */
class SystemConfig extends AdminBase
{
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
//        $module = request()->module();
//        $controller = request()->controller();
//        $action = request()->action();
//        $title = getTitleOne($module,$controller,$action);
        return array(
            'info'  => array(
                'name' => '网站全局设置',
                'description' => '网站全局设置',
            ),
            'menu' => array(
                array(
                    'name' => '网站信息',
                    'url' => url('site')
                )
            ),
            '_info' => array(
                array(
                    'name' => '添加配置项',
                    'url' => url('add'),
                )
            )
        );
    }

    /** 渲染输出全局配置项列表
     * @return mixed
     */
    public function _list(){
        $model = new SystemConfigModel();
        $configList = $model->order('id asc,orders asc')->paginate(20);
        $this->assign('config_list',$configList);
        $this->assign('show',true);
        return $this->fetch();
    }

    /**
     * 更新配置项排序
     */
    public function orders()
    {
        if($this->request->isPost()) {
            $post = $this->request->post();
            $i = 0;
            foreach ($post['id'] as $k => $val) {
                $order = (new SystemConfigModel())->where('id',$val)->value('orders');
                if($order != $post['orders'][$k]) {
                    if(false == (new SystemConfigModel())->where('id',$val)->update(['orders'=>$post['orders'][$k]])) {
                        $this->error('更新失败');
                    } else {
                        $i++;
                    }
                }
            }
            $this->success('成功更新'.$i.'个数据','admin/system_config/'.$post['system_action']);
        }
    }

    /** 更新配置项数值 value
     * @return \think\response\Json
     */
    public function update_value(){
        if ($this->request->isGet()==false) {
            return json(array('code' => 0, 'msg' => '更新失败'));
        }
        $get = $this->request->get();
        $model = new SystemConfigModel();
        if($get['type']=='checkbox'){
            $config=$model->find($get['id']);
            if($config['value'] == true) {
                if(strpos($config['value'],',') !==false) {
                    $arr = explode(',', $config['value']);
                    if (in_array($get['value'], $arr)) {
                        array_splice($arr,array_search($get['value'],$arr),1);
                    } else {
                        $arr[] = $get['value'];
                    }
                }elseif($config['value']==$get['value']){
                    $arr=[];
                    $get['value']='';
                }else{
                    $arr[]=$config['value'];
                    $arr[] = $get['value'];
                }
                if(count($arr)>=2){
                    $get['value'] = implode(',', $arr);
                }elseif(count($arr)==1){
                    $get['value'] = $arr[0];
                };
            }
        }
        if ($model->where('id', $get['id'])->update(['value' =>$get['value']]) !== false) {
            //  清空缓存
            Cache::clear();
            //记录日志
            $this->add_log($this->userSession['id'],$this->userSession['username'],'更新配置项：'.$model->where('id', $get['id'])->value('title').'值=》'.$get['value']);
            return json(array('code' => 200, 'msg' => '更新成功'));
        }
        // $this->error('更新失败');
        return json(array('code' => 0, 'msg' => '更新失败'));
    }

    /** 新增配置项
     * @param Request $request
     * @return mixed|\think\response\Json
     */
    public function add(Request $request){
        if ($request->isPost()){
            $model = new SystemConfigModel();
            //是提交操作
            $post = $this->request->post();
            //验证部分数据合法性
            $is_Check=(new SystemConfigValidate())->goCheck($post);
            if($is_Check !==true){
                return ajaxReturn(0,'提交失败：' . $is_Check);
            }
            //验证变量名称是否存在
            $nickname = $model->where('vari',$post['vari'])->select();
            if(!$nickname->isEmpty()) {
                return ajaxReturn(0,'提交失败：该变量名称已被占用');
            }

            if(false == $model->allowField(true)->save($post)) {
                return ajaxReturn(0,'添加配置失败');
            } else {
                //  清空缓存
                Cache::clear();
                //记录日志
                $this->add_log($this->userSession['id'],$this->userSession['username'],'新增：'.$post['vari'].'配置项');
                return ajaxReturn(200,'添加配置成功',url($this->request->param('group')));
            }
        }else{
            return $this->fetch('info');
        }
    }

    /** 修改配置项
     * @param Request $request
     * @return mixed|\think\response\Json
     */
    public function edit(Request $request){
        if ($request->isPost()){
            $model = new SystemConfigModel();
            //是提交操作
            $post = $this->request->post();
            //验证部分数据合法性
            $is_Check=(new SystemConfigValidate())->goCheck($post);
            if($is_Check !==true){
                //$this->error('提交失败：' . $is_Check);
                return ajaxReturn(0,'提交失败');
            }
            //验证变量名是否存在
//            $name = $model->where(['vari'=>$post['vari'],['id','neq',$post['id']]])->find();
            $name = $model->where('vari="'.$post['vari'].'" and id != "'.$post['id'].'"')->find();
            if(!empty($name)) {
                //$this->error('提交失败：该变量名已经存在！');
                return ajaxReturn(0,'提交失败：该变量名已经存在！');
            }
            if(false == $model->allowField(true)->save($post,['id'=>$post['id']])) {
                //$this->error('修改失败');
                return ajaxReturn(0,'修改失败');
            } else {
                //  清空缓存
                Cache::clear();
                //记录日志
                $this->add_log($this->userSession['id'],$this->userSession['username'],'修改：'.$post['vari'].'配置项');
                //$this->success('修改管理员信息成功','admin/system_config/_list');
                return ajaxReturn(200,'修改管理员信息成功',url($this->request->param('group')));
            }
        }else{
            //获取配置项id
            $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : $id;
            if($id == 0) {
                return ajaxReturn(0,'id不正能为空');
            }
            $model = new SystemConfigModel();
            //非提交操作
            $config_data = $model->where('id',$id)->find();
            $this->assign('config_data',$config_data);
            return $this->fetch('info');
        }

    }

    /** 删除配置项
     * @param int $id
     */
    public function delete($id=0){
        if($this->request->isAjax()) {
            $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : $id;
            if($id==0){
                $this->error('删除失败:未获取配置项ID');
            }
            $qr=(new SystemConfigModel())->where(['id'=>$id])->find();
            if(!empty($qr)) {
                if(false ==(new SystemConfigModel())->where('id',$id)->delete()) {
                    $this->error('删除失败');
                } else {
                    $this->success('删除成功');
                }
            }
        }
    }

    /** 渲染输出系统相关配置
     * @return mixed
     */
    public function system(){
        $config_lists=(new SystemConfigModel())->where('group','system')->paginate(20);
        $this->assign('config_list',$config_lists);
        return $this->fetch('index');
    }

    /** 渲染输出站点相关配置
     * @return mixed
     */
    public function site(){
        $config_lists=(new SystemConfigModel())->where('group','site')->paginate(20);
        $this->assign('config_list',$config_lists);
        return $this->fetch('index');
    }

    /** 渲染输出邮箱相关配置
     * @return mixed
     */
    public function email(){
        $config_lists=(new SystemConfigModel())->where('group','email')->paginate(20);
        $this->assign('config_list',$config_lists);
        return $this->fetch('index');
    }

    /** 渲染输出短信相关配置
     * @return mixed
     */
    public function sms(){
        $config_lists=(new SystemConfigModel())->where('group','sms')->paginate(20);
        $this->assign('config_list',$config_lists);
        return $this->fetch('index');
    }

    /** 渲染输出图片相关配置
     * @return mixed
     */
    public function images(){
        $config_lists=(new SystemConfigModel())->where('group','images')->paginate(20);
        $this->assign('config_list',$config_lists);
        return $this->fetch('index');
    }

    /** 渲染输出Home相关配置
     * @return mixed
     */
    public function home(){
        $config_lists=(new SystemConfigModel())->where('group','home')->paginate(20);
        $this->assign('config_list',$config_lists);
        return $this->fetch('index');
    }
}