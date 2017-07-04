<?php
namespace app\common\controller;
use think\Controller;

/*
* 系统公共类
*/
class Cms extends Controller
{
   public function _initialize()
   {
       header("Content-type:text/html;charset=utf-8");
       //后台错误级别
       error_reporting(E_ERROR | E_WARNING | E_PARSE);
       parent::_initialize();
   }
}