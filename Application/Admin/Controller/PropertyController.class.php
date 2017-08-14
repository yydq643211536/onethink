<?php
//物业管理
namespace Admin\Controller;

use Think\Page;

class PropertyController extends AdminController
{
    //物业管理首页
    public function index()
    {
//        $pid = I('get.pid', 0);
//        /* 获取频道列表 */
//        $map = array('status' => array('gt', -1));
//        $list = M('Property')->where($map)->select();

        $Property = M('Property'); // 实例化User对象
// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
        $list = $Property->page($_GET['p'].',6')->select();
        $this->assign('list',$list);// 赋值数据集
        import("ORG.Util.Page");// 导入分页类
        $count      = $Property->count();// 查询满足要求的总记录数
        $Page       = new Page($count,6);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->display(); // 输出模板

//        dump($list);exit;
        $this->assign('list', $list);
//        $this->assign('pid', $pid);
//        $this->meta_title = '维修管理';

        $this->display();




    }

    //后台手动添加
    public function add()
    {
        if (IS_POST) {
            $Property = D('Property');
            $data = $Property->create();
            if ($data) {
                //$Property->sn = uniqid("IT_");
                $id = $Property->add();
                if ($id) {
                    $this->success('新增成功', U('index'));
                    //var_dump($Property);exit;
                    //记录行为
                    action_log('update_property', 'property', $id, UID);
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Property->getError());
            }
        }else{
            $this->display('edit');
        }
    }

    //后台手动删除
    public function del()
    {
        $id = array_unique((array)I('id', 0));

        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id));
        if (M('Property')->where($map)->delete()) {
            //记录行为
            action_log('update_property', 'property', $id, UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    //后台手动修改
    public function edit($id = 0)
    {

        if (IS_POST) {
            $Property = D('Property');
            $data = $Property->create();
            if ($data) {
                if ($Property->save()) {
                    //记录行为
                    action_log('update_property', 'property', $data['id'], UID);
                    $this->success('编辑成功', U('index'));
                } else {
                    $this->error('编辑失败');
                }

            } else {
                $this->error($Property->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('Property')->find($id);

            if (false === $info) {
                $this->error('获取配置信息错误');
            }

            $pid = I('get.pid', 0);
            //获取父导航
            if (!empty($pid)) {
                $parent = M('Property')->where(array('id' => $pid))->field('title')->find();
                $this->assign('parent', $parent);
            }

            $this->assign('pid', $pid);
            $this->assign('info', $info);
            $this->meta_title = '编辑导航';
            $this->display();
        }
    }

    public function Page()
    {

        $Property = M('Property'); // 实例化User对象
// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
        $list = $Property->where('status=1')->page($_GET['p'].',2')->select();
        $this->assign('list',$list);// 赋值数据集
        import("ORG.Util.Page");// 导入分页类
        $count      = $Property->where('status=1')->count();// 查询满足要求的总记录数
        $Page       = new Page($count,2);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->display(); // 输出模板
    }

}