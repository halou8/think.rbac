<?php
namespace app\admin\controller;
use app\common\controller\Admin;
use think\Db;

class Config extends Admin
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function conf()
    {
        $id           = $this->request->param('id','web','trim');
        $config       = require CONF_PATH . '../config.php';	//网站配置
        if(file_exists(CONF_PATH . 'admin/config.php')) {
            $config_admin = require CONF_PATH . 'admin/config.php';	//后台分组配置
        }
        if(file_exists( CONF_PATH . 'home/config.php')) {
            $config_home  = require CONF_PATH . 'home/config.php';	//前台分组配置
        }
        if(file_exists(CONF_PATH . 'database.php')) {
            $config_db = require CONF_PATH . 'database.php';	//数据库配置
        }

        $this->assign('con',$config);
        $this->assign('con_admin',$config_admin);
        $this->assign('con_home',$config_home);
        $this->assign('con_db',$config_db);
        return $this->fetch($id);
    }

    // 配置信息保存
    private function updateconfig($config)
    {
        foreach ($config as $k => $c) {
            $config_old = array();
            $config_new = array();
            switch ($k) {
                case 'con':
                    $config_old = require CONF_PATH  . '../config.php';
                    if(is_array($c)) $config_new = array_merge($config_old,$c);
                    arr2file(CONF_PATH  . '../config.php',$config_new);
                    break;

                case 'con_admin':
                    $config_old = require CONF_PATH . 'admin/config.php';
                    if(is_array($c)) $config_new = array_merge($config_old,$c);
                    arr2file(CONF_PATH . 'admin/config.php',$config_new);
                    break;

                case 'con_home':
                    $config_old = require CONF_PATH . 'home/config.php';
                    if(is_array($c)) $config_new = array_merge($config_old,$c);
                    arr2file(CONF_PATH . 'home/config.php',$config_new);
                    break;
                case 'con_db':
                    $config_old = require CONF_PATH . 'database.php';
                    if(is_array($c)) $config_new = array_merge($config_old,$c);
                    arr2file(CONF_PATH . 'database.php',$config_new);
                    break;
            }

        }
        @unlink(RUNTIME_PATH . '/temp/~app.php');
        $this->success('更新成功！');
    }

    //更新web相关配置
    public function updateweb()
    {
        $con                      = $_POST["con"];
        if(isset($_POST["con_home"]))
            $con_home                 = $_POST["con_home"];
        if(isset($con['web_url']))
            $con['web_url']           = getaddxie($con['web_url']);
        if(isset($con['web_path']))
            $con['web_path']          = getaddxie($con['web_path']);
        if(isset($con['web_adsensepath']))
            $con['web_adsensepath']   = getrexie($con['web_adsensepath']);
        if(isset($con['web_copyright']))
            $con['web_copyright']     = stripslashes($con['web_copyright']);
        if(isset($con['web_tongji']))
            $con['web_tongji']        = stripslashes($con['web_tongji']);
        if(isset($con['web_admin_pagenum']))
            $con['web_admin_pagenum'] = abs(intval($con['web_admin_pagenum']));
        if(isset($con['web_home_pagenum']))
            $con['web_home_pagenum']  = abs(intval($con['web_home_pagenum']));
        if(isset($con['web_adsensepath'])){
            $dir                      = './'.$con['web_adsensepath'];	//广告保存目录
            if(!is_dir($dir)){
                mkdirss($dir);
            }
        }

        if(isset($con_home)){
            $config = array('con'=>$con,'con_home'=>$con_home);
        }else{
            $config = array('con'=>$con);
        }
        $this->updateconfig($config);
    }

    //更新数据库链接配置
    public function updatedb()
    {
        $con_db = $_POST["con_db"];
        $con_db['hostport'] = abs(intval($con_db['hostport']));
        $config = ['con_db' => $con_db];
        $this->updateconfig($config);
    }
}