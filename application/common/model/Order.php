<?php
namespace app\common\model;
use think\exception\DbException;
use think\Model;

/**
 * Class Shili
 * @package app\common\model
 */
class Order extends Model {
    protected $autoWriteTimestamp = true;
    protected $pk = 'id';
//    protected $createTime = 'add_time';
    /*
     * 列表
     */
    public function loadList($type='all', $where=array(), $field='A.*,B.name member_name,B.nickname,B.mobile member_mobile', $limit=15, $order='id DESC'){
        $object = self::alias('A')
            ->join('member B','A.member_id=B.id')
            ->where($where)
            ->field($field)
            ->order($order);
        if ($type=='all'){
            $object=$object->limit($limit)->select();
        }else{
            $object=$object->paginate($limit);
        }
        return $object;
    }

    /** 主键id获取信息
     * @param $id
     * @return array
     * @throws DbException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getInfo($id)
    {
        $map = array();
        $map[]=['A.'.$this->pk,'=',$id];
        return $this->getWhereInfo($map);
    }

    /** Where条件获取信息
     * @param array $where
     * @param string $field
     * @return array
     * @throws DbException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getWhereInfo($where=array(),$field='A.*,B.name member_name,B.nickname,B.mobile member_mobile')
    {
        $info=self::alias('A')
            ->join('member B','A.member_id=B.id')
            ->where($where)
            ->field($field)
            ->find();
        return $info ? $info->toArray() : array();
    }

    /** 新增
     * @param array $data
     * @return static
     */
    public function add($data=array())
    {
        if (empty($data)){
            $data=$_POST;
        }
        return self::create($data,true);
    }

    /** 修改
     * @param array $data
     * @param array $where
     * @return static
     */
    public function edit($data=array(),$where=array())
    {
        if (empty($data)){
            $data=$_POST;
        }
        $where[]=[$this->pk,'=',$_POST[$this->pk]];
        return self::update($data,$where,true);
    }

    /** 主键id删除
     * @param int $id ID
     * @return bool 删除状态
     */
    public function del($id)
    {
        $map[] = [$this->pk,'=',$id];
        return self::where($map)->delete();
    }

    public function product()
    {
    	return $this->hasOne('Product','id','product_id');
    }
}
