<?php
namespace app\common\controller;
/**
 * 前台入口模块
 *
 */
class Home extends Cms
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     *  空操作拦截
     */
    public function _empty($action)
    {
        $this->error("方法不存在:" . get_class($this) . '->' . $action . '()');
    }
}