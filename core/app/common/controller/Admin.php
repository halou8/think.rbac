<?php
namespace app\common\controller;
use library\org\util\RBAC;
use think\Request;

/*
* Admin分组公共类
*/
class Admin extends Cms
{
    public function _initialize()
    {
        parent::_initialize();
        // 后台用户权限检查
        if (config('USER_AUTH_ON') && !in_array($this->request->controller(), explode(',', config('NOT_AUTH_MODULE')))) {
            if (!RBAC::AccessDecision()) {
                //检查认证识别号
                if (!session(config('USER_AUTH_KEY'))) {
                    //跳转到认证网关
                    $this->redirect(url(config('USER_AUTH_GATEWAY')));
                }

                // 没有权限 抛出错误
                if (config('RBAC_ERROR_PAGE')) {
                    // 定义权限错误页面
                    return redirect(config('RBAC_ERROR_PAGE'));
                } else {
                    if (config('GUEST_AUTH_ON')) {
                        $this->assign('jumpUrl', url('USER_AUTH_GATEWAY'));
                    }
                    // 提示错误信息
                    $this->error(lang('_valid_access_'));
                }
            }

        }

    }
}