<?php
namespace app\admin\controller;
use app\common\controller\Admin;
use think\Db;
use library\Tree;

class User extends Admin
{
    public function _initialize()
    {
        parent::_initialize();  //RBAC 验证接口初始化
    }

    public function index()
    {
        $role = model('Role')->column('id,name');

        $map = array();
        $UserDB = model('User');
        $list = $UserDB->where($map)->paginate(config('web_admin_pagenum'));
        $this->assign('role',$role);
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function add()
    {
        $UserDB = model("User");

        if(isset($_POST['dosubmit'])) {
            $password = $_POST['password'];
            $repassword = $_POST['repassword'];
            if(empty($password) || empty($repassword)){
                $this->error('密码必须！');
            }
            if($password != $repassword){
                $this->error('两次输入密码不一致！');
            }

            //根据表单提交的POST数据创建数据对象
            $userInfo = $UserDB->create($_POST);
            if(isset($userInfo[$userInfo->getPk()])){
                $data['user_id'] = $userInfo->id;
                $data['role_id'] = input('post.role');

                if(model('RoleUser')->addRoleUser($data)) {
                    $this->success('添加成功！','/Admin/User/index');
                } else {
                    $this->error('用户添加成功,但角色对应关系添加失败!');
                }
            } else {
                $this->error($userInfo->getError());
            }
        } else{
            $role = model('Role')->getAllRole(array('status'=>1),'sort DESC');
            $this->assign('role',$role);
            $this->assign('tpltitle','添加');
            return $this->fetch();
        }
    }

    // 编辑用户
    public function edit(){
        $UserDB = model("User");
        if(isset($_POST['dosubmit'])) {;
            $password = $_POST['password'];
            $repassword = $_POST['repassword'];
            if(!empty($password) || !empty($repassword)){
                if($password != $repassword){
                    $this->error('两次输入密码不一致！');
                }
            }

            if(empty($password) && empty($repassword)) unset($_POST['password']);   //不填写密码不修改

            $userInfo = $UserDB->update($_POST);
            //根据表单提交的POST数据创建数据对象
            if(isset($userInfo[$userInfo->getPk()])){
                $where['user_id'] = $this->request->post('id');
                $data['role_id'] = $this->request->post('role');

                model('RoleUser')->upRoleUser($where,$data);

                $this->success('编辑成功！',url('/Admin/User/index'));
            } else {
                $this->error($userInfo->getError());
            }
        }else{
            $id = input('param.id',0,'intval');
            if(!$id)$this->error('参数错误!');
            $role = model('Role')->getAllRole(array('status'=>1),'sort DESC');
            $info = $UserDB->getUser(array('id'=>$id));
            $this->assign('tpltitle','编辑');
            $this->assign('role',$role);
            $this->assign('info',$info);
            return $this->fetch('add');
        }
    }

    //ajax 验证用户名
    public function check_username(){
        $userid = $this->request->param('userid','0','intval');
        $username =  $this->request->param('username');
        if(model("User")->check_name($username,$userid)){
            echo 1;
        }else{
            echo 0;
        }
    }

    //删除用户
    public function del(){
        $id = input('param.id',0,'intval');
        if(!$id)$this->error('参数错误!');
        $UserDB = model('User');
        $info = $UserDB->getUser(array('id'=>$id));

        if($info['username'] == config('SPECIAL_USER')){     //无视系统权限的那个用户不能删除
            $this->error('禁止删除此用户!');
        }

        if($UserDB->delUser('id='.$id)){
            if(model("RoleUser")->where('user_id='.$id)->delete()){
                $this->success('删除成功！',url('/Admin/User/index'));
            }else{
                $this->error('用户成功,但角色对应关系删除失败!');
            }
        } else{
            $this->error('删除失败!');
        }
    }

    /* ========角色部分======== */

    // 角色管理列表
    public function role(){
        $RoleDB = model('Role');
        $list = $RoleDB->getAllRole();
        $this->assign('list',$list);
        return $this->fetch();
    }

    // 添加角色
    public function role_add(){
        $RoleDB = model("Role");
        if(isset($_POST['dosubmit'])) {
            //根据表单提交的POST数据创建数据对象
            $roleInfo = $RoleDB->create(input('post.'));

            if(isset($roleInfo[$roleInfo->getPk()])) {
                $this->success('添加成功！',url('/Admin/User/role'));
            }else{
                $this->error($roleInfo->getError());
            }
        }else{
            $this->assign('tpltitle','添加');
            return $this->fetch();
        }
    }

    // 编辑角色
    public function role_edit(){
        $RoleDB = model("Role");
        if(isset($_POST['dosubmit'])) {
            //根据表单提交的POST数据创建数据对象
            $roleInfo = $RoleDB->update(input('post.'));

            if(isset($roleInfo[$roleInfo->getPk()])) {
                $this->success('编辑成功！',url('/Admin/User/role'));
            } else {
                $this->error($roleInfo->getError());
            }
        }else{
            $id = input('id',0,'intval');
            if(!$id)$this->error('参数错误!');
            $info = $RoleDB->getRole(array('id'=>$id));
            $this->assign('tpltitle','编辑');
            $this->assign('info',$info);
            return $this->fetch('user_role_add');
        }
    }

    //删除角色
    public function role_del(){
        $id = input('param.id',0,'intval');
        if(!$id)$this->error('参数错误!');
        $RoleDB = model('Role');
        if($RoleDB->delRole('id='.$id)){
            $this->success('删除成功！',url('/Admin/User/role'));
        }else{
            $this->error('删除失败!');
        }
    }

    // 排序权重更新
    public function role_sort(){
        $sorts = $_POST['sort'];
        if(!is_array($sorts))$this->error('参数错误!');
        foreach ($sorts as $id => $sort) {
            model('Role')->upRole( ['id'=>$id , 'sort'=>intval($sort)] );
        }
        $this->success('更新完成！',url('/Admin/User/role'));
    }

    /* ========权限设置部分======== */

    //权限浏览
    public function access(){
        $roleid = $this->request->param('roleid',0,'intval');
        if(!$roleid) $this->error('参数错误!');

        $Tree = new Tree();
        $Tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
        $Tree->nbsp = '&nbsp;&nbsp;&nbsp;';

        $NodeDB = model('Node');
        $node = obj2arr($NodeDB->getAllNode());

        $AccessDB = model('Access');
        $access = obj2arr($AccessDB->getAllAccess('','role_id,node_id,pid,level'));


        foreach ($node as $n=>$t) {
            $node[$n]['checked'] = ($AccessDB->is_checked($t,$roleid,$access))? ' checked' : '';
            $node[$n]['depth'] = $AccessDB->get_level($t['id'],$node);
            $node[$n]['pid_node'] = ($t['pid'])? ' class="tr lt child-of-node-'.$t['pid'].'"' : '';
        }
        $str  = "<tr id='node-\$id' \$pid_node>
                    <td style='padding-left:30px;'>\$spacer<input type='checkbox' name='nodeid[]' value='\$id' class='radio' level='\$depth' \$checked onclick='javascript:checknode(this);' > \$title (\$name)</td>
                </tr>";

        $Tree->init($node);
        $html_tree = $Tree->get_tree(0, $str);
        $this->assign('html_tree',$html_tree);

        $this->assign('roleid',$roleid);
        return $this->fetch();
    }

    //权限编辑
    public function access_edit(){
        $roleid = $this->request->post('roleid',0,'intval');
        $nodeid = $_POST['nodeid'];
        if(!$roleid) $this->error('参数错误!');
        $AccessDB = model('Access');

        if (is_array($nodeid) && count($nodeid) > 0) {  //提交得有数据，则修改原权限配置
            $AccessDB -> delAccess(array('role_id'=>$roleid));  //先删除原用户组的权限配置

            $NodeDB = model('Node');
            $node = obj2arr($NodeDB->getAllNode());

            foreach ($node as $_v) $node[$_v[id]] = $_v;
            foreach($nodeid as $k => $node_id){
                $data[$k] = $AccessDB -> get_nodeinfo($node_id,$node);
                $data[$k]['role_id'] = $roleid;
            }

            $AccessDB->saveAll($data);   // 重新创建角色的权限配置
        } else {    //提交的数据为空，则删除权限配置
            $AccessDB->delAccess(array('role_id'=>$roleid));
        }
        $this->success('设置成功！',url('/Admin/User/access',array('roleid'=>$roleid)));
    }


}