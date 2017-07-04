<?php
namespace app\home\controller;
use think\Controller;
use think\Request;
/*
* 空模块
* 前台模块指定错误时调用
*/
class Error extends Controller
{
    public function index(Request $request)
    {
        $this->error("控制器不存在:" . $request->controller());
    }
}