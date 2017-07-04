<?php
namespace app\admin\controller;
use app\common\controller\Admin;
use library\org\io\Dir;

class Cache extends Admin
{
    public function _initialize()
    {
        parent::_initialize();	//RBAC 验证接口初始化
    }

    // 删除全部核心缓存
    public function delCore()
    {
        $dir = new Dir();
        @unlink(RUNTIME_PATH . '/~runtime.php');		//删除主编译缓存文件
        @unlink(RUNTIME_PATH . '/~crons.php');		//删除计划任务缓存文件
        @unlink(RUNTIME_PATH . '/cron.lock');		//删除计划任务执行锁定文件
        if(is_dir(RUNTIME_PATH . '/data')){$dir->delDir(RUNTIME_PATH . '/data');}
        if(is_dir(RUNTIME_PATH . '/temp')){$dir->delDir(RUNTIME_PATH . '/temp');}
        if(is_dir(RUNTIME_PATH . '/cache')){$dir->delDir(RUNTIME_PATH . '/cache');}
        if(is_dir(RUNTIME_PATH . '/log')){$dir->delDir(RUNTIME_PATH . '/log');}
        echo('[清除成功]');
    }

}