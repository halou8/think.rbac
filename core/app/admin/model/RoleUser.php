<?php
namespace app\admin\model;
use app\common\model\Admin;

class RoleUser extends Admin
{

    public function upRoleUser($where,$data)
    {
        if($where) {
            return $this->where($where)->update($data);
        } else {
            return false;
        }
    }

    public function addRoleUser($data)
    {
        if($data) {
            return $this->create($data);
        } else {
            return false;
        }
    }
}