<?php
namespace app\admin\controller;

use app\common\controller\Admin;
use library\Tree;
use think\Request;

class Node extends Admin
{
    public function _initialize()
    {
        parent::_initialize();
    }

    //菜单列表
    public function index()
    {
        $Node = obj2arr(model('Node')->getAllNode());
        $array = array();
        // 构建生成树中所需的数据
        foreach($Node as $k => $r) {
            $r['id']      = $r['id'];
            $r['title']   = $r['title'];
            $r['name']    = $r['name'];
            $r['status']  = $r['status']==1 ? '<font color="red">√</font>' :'<font color="blue">×</font>';
            $r['submenu'] = $r['level']==3 ? '<font color="#cccccc">添加子菜单</font>' : "<a href='".url('/Admin/Node/add/pid/'.$r['id'])."'>添加子菜单</a>";
            $r['edit']    = $r['level']==1 ? '<font color="#cccccc">修改</font>' : "<a href='".url('/Admin/Node/edit/id/'.$r['id'].'/pid/'.$r['pid'])."'>修改</a>";
            $r['del']     = $r['level']==1 ? '<font color="#cccccc">删除</font>' : "<a onClick='return confirmurl(\"".url('/Admin/Node/del/id/'.$r['id'])."\",\"确定删除该菜单吗?\")' href='javascript:void(0)'>删除</a>";
            switch ($r['display']) {
                case 0:
                    $r['display'] = '不显示';
                    break;
                case 1:
                    $r['display'] = '主菜单';
                    break;
                case 2:
                    $r['display'] = '子菜单';
                    break;
            }
            switch ($r['level']) {
                case 0:
                    $r['level'] = '非节点';
                    break;
                case 1:
                    $r['level'] = '应用';
                    break;
                case 2:
                    $r['level'] = '模块';
                    break;
                case 3:
                    $r['level'] = '方法';
                    break;
            }
            $array[]      = $r;
        }

        $str  = "<tr class='tr'>
				    <td align='center'><input type='text' value='\$sort' size='3' name='sort[\$id]'></td>
				    <td align='center'>\$id</td> 
				    <td >\$spacer \$title (\$name)</td> 
				    <td align='center'>\$level</td> 
				    <td align='center'>\$status</td> 
				    <td align='center'>\$display</td> 
					<td align='center'>
						\$submenu | \$edit | \$del
					</td>
				  </tr>";

        $Tree = new Tree();
        $Tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
        $Tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        $Tree->init($array);
        $html_tree = $Tree->get_tree(0, $str);

        $this->assign('html_tree',$html_tree);
        return $this->fetch();
    }

    //添加菜单
    public function add()
    {
        if(isset($_POST['dosubmit'])) {
            $NodeDB = model("Node");
            //根据表单提交的POST数据创建数据对象
            $nodeInfo = $NodeDB->create($this->request->post());
            if(isset($nodeInfo[$nodeInfo->getPk()])){
                $this->success('添加成功！','/Admin/Node/index');
            }else{
                $this->error($nodeInfo->getError());
            }
            exit();
        }else{
            $Node = obj2arr(model('Node')->getAllNode());
            $pid = input('pid','intval',0);	//选择子菜单
            $array = array();
            foreach($Node as $k => $r) {
                $r['id']         = $r['id'];
                $r['title']      = $r['title'];
                $r['name']       = $r['name'];
                $r['disabled']   = $r['level']==3 ? 'disabled' : '';
                $array[$r['id']] = $r;
            }
            $str  = "<option value='\$id' \$selected \$disabled >\$spacer \$title</option>";
            $Tree = new Tree();
            $Tree->init($array);
            $select_categorys = $Tree->get_tree(0, $str, $pid);
            $this->assign('tpltitle','添加');
            $this->assign('select_categorys',$select_categorys);
            return $this->fetch();
        }

    }

    //编辑菜单
    public function edit()
    {
        $NodeDB = model('Node');
        if(isset($_POST['dosubmit'])) {
            //根据表单提交的POST数据创建数据对象
            $nodeInfo = $NodeDB->update($this->request->post());
            if(isset($nodeInfo[$nodeInfo->getPk()])){
                $this->success('编辑成功！','/Admin/Node/index');
            }else{
                $this->error($nodeInfo->getError());
            }
        } else {
            $id = input('id','0','intval');
            $pid = input('pid','0','intval');	//选择子菜单
            if(!$id || !$pid)$this->error('参数错误!');

            $allNode = obj2arr($NodeDB->getAllNode());
            $array = array();
            foreach($allNode as $k => $r) {
                $r['id']         = $r['id'];
                $r['title']      = $r['title'];
                $r['name']       = $r['name'];
                $r['disabled']   = $r['level']==3 ? 'disabled' : '';
                $array[$r['id']] = $r;
            }
            $str  = "<option value='\$id' \$selected \$disabled >\$spacer \$title</option>";
            $Tree = new Tree();
            $Tree->init($array);
            $select_categorys = $Tree->get_tree(0, $str, $pid);
            $this->assign('tpltitle','编辑');
            $this->assign('select_categorys',$select_categorys);
            $this->assign('info', $NodeDB->getNode('id='.$id));
            return $this->fetch('add');
        }

    }

    //删除菜单
    public function del()
    {
        $id = input('id','0','intval');
        if(!$id)$this->error('参数错误!');
        $NodeDB = model('Node');
        $info = $NodeDB -> getNode(array('id'=>$id),'id');
        if($NodeDB->childNode($info['id'])){
            $this->error('存在子菜单，不可删除!');
        }
        if($NodeDB->delNode('id='.$id)){
            $this->success('删除成功！',url('/Admin/Node/index'));
        }else{
            $this->error('删除失败!');
        }
    }

    //菜单排序权重更新
    public function sort()
    {
        $sorts = $_POST['sort'];
        if(!is_array($sorts))$this->error('参数错误!');
        foreach ($sorts as $id => $sort) {
            model('Node')->upNode( array('id' =>$id , 'sort' =>intval($sort) ) );
        }
        $this->success('更新完成！',url('/Admin/Node/index'));
    }
}