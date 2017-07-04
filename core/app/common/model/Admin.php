<?php
namespace app\common\model;

/*
* Admin分组模型公共类
*/
class Admin extends Cms
{
    //自动过滤掉非本表字段
    protected $field = true;
}