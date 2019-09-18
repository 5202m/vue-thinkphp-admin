<?php
/**
 * Created by PhpStorm.
 * User: Jade.Zhu
 * Date: 2019/9/18
 * Time: 10:11
 */
namespace app\admin\model;

use think\Model;

class Category extends Model
{

    public function getCategories($data = [])
    {
        $res = $this->where($data)->order('sort_order', 'asc')->select();
        if ($res) {
            $res = $res->toArray();
        }
        return $res;
    }

    public function getCategoryById($id = null)
    {
        $res = $this->getById($id);
        if ($res) {
            return $res;
        } else {
            $this->error = '当前查询分类不存在';
            return false;
        }
    }

    public function getCategoryByIds($ids = [])
    {
        $data = [
            'id' => array('in',$ids),
            //'status' => 1
        ];
        $res = $this->where($data)->order('sort_order', 'asc')->select();
        if ($res) {
            if ($res) {
                $res = $res->toArray();
            }
            return $res;
        } else {
            $res = [];
        }
    }

    public function saveCategory($param = [])
    {
        $validate = validate($this->name);
        if (!$validate->check($param)) {
            $this->error = $validate->getError();
            return false;
        }
        try {
            $this->data($param)->allowField(true)->save();
            return true;
        } catch (\Exception $e) {
            $this->error = '添加失败';
            return false;
        }
    }

    public function updateCategory($id = null, $param = [], $flag = true)
    {
        if ($flag) {
            $validate = validate($this->name);
            if (!$validate->check($param)) {
                $this->error = $validate->getError();
                return false;
            }
        }
        try {
            $this->allowField(true)->save($param, [$this->getPk() => $id]);
            return true;
        } catch (\Exception $e) {
            $this->error = '更新失败';
            return false;
        }
    }

    public function del($id = 0)
    {
        try {
            $res = $this->where('cat_id', $id)->delete();
            if ($res) {
                return $res;
            } else {
                $this->error = '删除失败';
                return false;
            }
        } catch (\Exception $e) {
            $this->error = '删除失败';
            return false;
        }
    }
}