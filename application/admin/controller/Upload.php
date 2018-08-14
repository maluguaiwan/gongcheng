<?php
namespace app\admin\controller;
use app\common\model\Upload as UploadModel;
use app\common\controller\AdminBase;
use think\facade\Session;

class Upload extends AdminBase
{
	function initialize()
	{

		parent::initialize();

	}
	public function upload_image()
	{
		$model =new UploadModel();
		$id = Session::has('adminUser.id') ? Session::get('adminUser.id') : 0;
		return json($model->upfile('images','file',false,false,$id));
	}
	public function upfile()
	{
		$model =new UploadModel();
		return json($model->upfile('files'));
	}
	public function layedit_upimage()
	{
		$model =new UploadModel();
		$result = $model->upfile('layedit', 'file', true);
		if ($result['code'] == 200) {
			$data = array('code' => 0, 'msg' => '上传成功', 'data' => array('src' => $result['path'], 'title' => $result['info']['name']));
		} else {
			$data = array('code' => 1, 'msg' => $result['msg']);
		}
		return json($data);
	}
	public function umeditor_upimage()
	{
		$model =new UploadModel();
		$result = $model->upfile('umeditor', 'upfile', true);
		if ($result['code'] == 200) {
			$data = array("originalName" => $result['info']['name'], "name" => $result['savename'], "url" => $result['path'], "size" => $result['info']['size'], "type" => $result['info']['type'], "state" => "SUCCESS");
		} else {
			$data = array("originalName" => $result['info']['name'], "name" => $result['savename'], "url" => $result['path'], "size" => $result['info']['size'], "type" => $result['info']['type'], "state" => $result['msg']);
		}
		echo json_encode($data);
		exit;
	}
}