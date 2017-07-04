<?php
namespace app\admin\controller;
use app\common\controller\Admin;
use library\org\util\RBAC;
use library\org\net\IpLocation;

class Login extends Admin
{
    //登陆界面
    public function index()
    {
        //检测后台登录入口是否正确
        return $this->fetch();
    }

    // 登录检测
    public function checkLogin()
    {
        $username =input('post.username');
        $password =  input('post.password');
        $verify   = input('post.verify');

        if (empty($username)) {
            $this->error(lang('lan_input_user_name'));
        }elseif(empty($password)) {
            $this->error(lang('lan_input_password'));
        }elseif(empty($verify)){
            $this->error(lang('lan_input_verify'));
        }

        //生成认证条件
        $map            =   array();
        // 支持使用绑定帐号登录
        $map['username'] = $username;
        $map['status']        = 1;

        if(!captcha_check($verify)){
            $this->error('验证码错误！');
        };
        $authInfo = RBAC::authenticate($map);
        //使用用户名、密码和状态的方式进行认证
        if(false == $authInfo) {
            $this->error('帐号不存在或已禁用！');
        }else {
            if($authInfo['password'] != md5($password) ) {
                $this->error('密码错误！');
            }
            session(config('USER_AUTH_KEY'), $authInfo['id']);
            session('userid',$authInfo['id']);  //用户ID
            session('username',$authInfo['username']);   //用户名
            session('roleid',$authInfo['role']);    //角色ID
            if($authInfo['username']==config('SPECIAL_USER')) {
                session(config('ADMIN_AUTH_KEY'), true);
            }
            //保存登录信息
            $User	=	model(config('USER_AUTH_MODEL'));
            $ip		=	$this->request->ip();
            $data = array();
            if($ip){    //如果获取到客户端IP，则获取其物理位置
                $Ip = new IpLocation(); // 实例化类
                $location = $Ip->getlocation($ip); // 获取某个IP地址所在的位置
                $data['last_location'] = '';
                if($location['country'] && $location['country']!='CZ88.NET') $data['last_location'].=$location['country'];
                if($location['area'] && $location['area']!='CZ88.NET') $data['last_location'].=' '.$location['area'];
            }

            $data['last_login_time']	=	time();
            $data['last_login_ip']	=	$this->request->ip();

            $User->where(['id' => $authInfo['id']])->update($data);
            // 缓存访问权限
            RBAC::saveAccessList($authInfo['id']);
            return redirect('Index/index');
        }
    }

    // 用户登出
    public function logout()
    {
        if(session('?'.config('USER_AUTH_KEY'))) {
            session(config('USER_AUTH_KEY'),null);
            session(null);
            return redirect('Login/index');
        } else {
            return $this->error('已经登出！');
        }
    }
}