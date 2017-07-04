<?php
namespace app\admin\controller;
use app\common\controller\Admin;
use think\Db;
use think\Model;

class Index extends Admin
{
    public function index()
    {
        $username = session('username');    // 用户名
        $roleid   = session('roleid');      // 角色ID
        if($username == config('SPECIAL_USER')){     //如果是无视权限限制的用户，则获取所有主菜单
            $sql = 'SELECT `id`,`title` FROM `tp_node` WHERE ( `status` =1 AND `display`=1 AND `level`<>1 ) ORDER BY sort DESC';
        }else{  //更具角色权限设置，获取可显示的主菜单
            $sql = "SELECT `tp_node`.`id` as id,`tp_node`.`title` as title FROM `tp_node`,`tp_access` WHERE `tp_node`.id=`tp_access`.node_id AND `tp_access`.role_id=$roleid  AND  `tp_node`.`status` =1 AND `tp_node`.`display`=1 AND (`tp_node`.`level` =0 OR `tp_node`.`level` =2)  ORDER BY `tp_node`.sort DESC";
        }

        $main_menu = Db::query($sql);
        $this->assign('main_menu',$main_menu);
        return $this->fetch();
    }

    public function left()
    {
        $pid = input('param.id',0,"intval");    //选择子菜单
        $NodeDB = model('Node');
        $datas = $this->left_child_menu($pid);

        $parent_info = $NodeDB->getNode(array('id'=>$pid),'title');
        $sub_menu_html = '<dl>';
        foreach($datas as $key => $_value) {
            $sub_array = $this->left_child_menu($_value['id']);
            $sub_menu_html .= "<dt><a target='_self' href='#' onclick=\"showHide('{$key}');\">{$_value['title']}</a></dt><dd><ul id='items{$key}'>";
            if(is_array($sub_array)){
                foreach ($sub_array as $value) {
                    $href = empty($value['data']) ? 'javascript:void(0)' : url($value['data']);
                    $sub_menu_html .= "<li><a id='a_{$value['id']}' onClick='sub_title({$value['id']})' href='{$href}'>{$value['title']}</a></li>";
                }
            }
            $sub_menu_html .=  '</ul></dd>';
        }
        $sub_menu_html .= '</dl>';

        $this->assign('sub_menu_title',$parent_info['title']);
        $this->assign('sub_menu_html',$sub_menu_html);
        return $this->fetch();

    }

    /**
     * 按父ID查找菜单子项
     * @param integer $parentid   父菜单ID
     * @param integer $with_self  是否包括他自己
     */
    private function left_child_menu($pid, $with_self = 0)
    {
        $pid = intval($pid);

        $username = session('username');    // 用户名
        $roleid   = session('roleid');      // 角色ID
        if($username == config('SPECIAL_USER')){     //如果是无视权限限制的用户，则获取所有主菜单
            $sql = "SELECT `id`,`data`,`title` FROM `tp_node` WHERE ( `status` =1 AND `display`=2 AND `level` <>1 AND `pid`=$pid ) ORDER BY sort DESC";
        }else{
            $sql = "SELECT `tp_node`.`id` as `id` , `tp_node`.`data` as `data`, `tp_node`.`title` as `title` FROM `tp_node`,`tp_access` WHERE `tp_node`.id = `tp_access`.node_id AND `tp_access`.role_id = $roleid AND `tp_node`.`pid` =$pid AND `tp_node`.`status` =1 AND `tp_node`.`display` =2 AND `tp_node`.`level` <>1 ORDER BY `tp_node`.sort DESC";
        }

        $result = Db::query($sql);

        if($with_self) {
            $NodeDB = D('Node');
            $result2[] = $NodeDB->getNode(array('id'=>$pid),`id`,`data`,`title`);
            $result = array_merge($result2,$result);
        }
        return $result;
    }

    public function top()
    {
        return $this->fetch();
    }

    public function main()
    {
        return $this->fetch();
    }

    public function footer()
    {
        return $this->fetch();
    }
}