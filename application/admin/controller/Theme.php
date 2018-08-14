<?php
namespace app\admin\controller;
use app\common\controller\AdminBase;
use app\common\model\Theme as ThemeModel;
use think\facade\Cache;

class Theme extends AdminBase
{
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '主题管理',
                'description' => '管理网站主题',
            ),
            'menu' => array(
                array(
                    'name' => '主题列表',
                    'url' => url('index'),
                    'icon' => 'list',
                )
            )
        );
    }
    public function index()
    {
        $themeModel=new ThemeModel();
        $themeSelect=$themeModel->select();//查询数据库所有模板
        $moduleDir=getNextDir(ROOT_PATH.'template');//查询当前template下的模型文件夹名称
        $themeDir=[];
        //判断数据库安装主题是否存在本地
        foreach($themeSelect as $kk=> $find){//遍历数据库的模板列表
            $find['dir']=0;//模板文件夹不存在
            $find['install']=1;//已安装
            foreach($moduleDir as $key=> $dir){
                if($find['module']==$dir['dir']){//遍历如果数据库的模块 匹配 当前目录模型
                    $themeName=getNextDir(ROOT_PATH.'template/'.$dir['dir']);//获取该模块下的所有文件夹
                    foreach($themeName as $dirTheme){ //遍历模块下的所有文件夹
                        if($dirTheme['dir']==$find['name']){ // 如果模板文件夹名称与数据库文件夹 匹配
                            $find['dir']=1; //模板文件夹存在
                            $find['install']=1;//已安装
                        }
                    }
                }
            }
            $themeDir[$kk]=$find;//赋值
        }
        //判断本地是否有为安装的主题
        foreach($moduleDir as $key=> $dir){//遍历模板下的文件夹
            $themeName=getNextDir(ROOT_PATH.'template/'.$dir['dir']);//获取本地模块下的模板列表
            foreach($themeName as $dirTheme) {//遍历本地模板
                $fileName=ROOT_PATH.'template/'.$dir['dir'].'/'.$dirTheme['dir'].'/info.php';//模板文件信息
                if( file_exists($fileName) ){//如果文件存在
                    $require = require  $fileName;
                    //查询数据库是否存在该本地模板
                    $findTrue=$themeModel->where(['module'=>$dir['dir'],'name'=>$dirTheme['dir']])->find();
                    if($findTrue==false){//如果不存在
                        $require['status']=0; //状态0
                        $require['dir']=1; //目录存在
                        $require['install']=0; //未安装
                        $themeDir[]=$require;
                    }
                }
            }
        }
        $this->assign('theme',$themeDir);
        return $this->fetch();
    }

    /**  切换应用主题
     * @return \think\response\Json
     */
    public function update_status(){
        if ($this->request->isGet()) {
            $get = $this->request->get();
            $model = new ThemeModel();
            if($get['status']==1){
                if ($model->where('module', $get['module'])->update(['status' =>0]) !== false and $model->where('id', $get['id'])->update(['status' =>$get['status']]) !== false) {
                    //  清空缓存
                    Cache::clear();
                    //记录日志
                    $this->add_log($this->userSession['id'],$this->userSession['username'],'开启'.$get['module'].'的主题：'.$model->where('id', $get['id'])->value('name'));
                    //  $this->success('更新成功');
                    return json(array('code' => 200, 'msg' => '启用成功'));
                }

            }elseif ($model->where('id', $get['id'])->update(['status' =>$get['status']]) !== false) {
                //  清空缓存
                Cache::clear();
                //记录日志
                $this->add_log($this->userSession['id'],$this->userSession['username'],'关闭'.$get['module'].'的主题：'.$model->where('id', $get['id'])->value('name'));
                //  $this->success('更新成功');
                return json(array('code' => 200, 'msg' => '关闭成功'));
            }
        }
        // $this->error('更新失败');
        return json(array('code' => 0, 'msg' => '提交失败'));
    }

    public function update_install(){
        if ($this->request->isGet()) {
            $get = $this->request->get();
            $model = new ThemeModel();
            if($get['status']==0){
               if($model->where(['id'=>$get['id']])->delete() !=0){
                   //记录日志
                   $this->add_log($this->userSession['id'],$this->userSession['username'],'卸载主题ID:'.$get['id']);
                   return json(array('code' => 200, 'msg' => '卸载成功'));
               };
            }elseif($get['status']==1){
               $dd=require ROOT_PATH.'template/'.$get['module'].'/'.$get['name'].'/info.php';
                if ($model->allowField(true)->save($dd) !== false ) {
                    //记录日志
                    $this->add_log($this->userSession['id'],$this->userSession['username'],'安装主题ID:'.$get['id']);
                    //  $this->success('更新成功');
                    return json(array('code' => 200, 'msg' => '安装成功'));
                }
            }
        }
        // $this->error('更新失败');
        return json(array('code' => 0, 'msg' => '提交失败'));
    }
    public function delete_theme(){
        if ($this->request->isAjax()) {
            $post = $this->request->param();
            $fileName = explode('&', $post['id']);
            if(removeDir(ROOT_PATH.'template/'.$fileName[0].'/'.$fileName[1])){
                //记录日志
                $this->add_log($this->userSession['id'],$this->userSession['username'],'删除主题ID:'.$post['id']);
                $this->success('删除成功');
            }
        }
        $this->error('删除失败');
    }

}