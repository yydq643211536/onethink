<?php
/**
 * Created by PhpStorm.
 * User: 64321
 * Date: 2017/8/11
 * Time: 15:12
 */

namespace Home\Controller;


use Think\Controller;

class PropertyController extends Controller
{
    //报修
    public function add()
    {

        if (IS_POST) {
            $Property = D('Property');
            $data = $Property->create();
            //$Repair->member_id = session('user_auth')['uid'];
            if ($data) {
                $id = $Property->add();
                if ($id) {
                    $this->success('新增成功', U('Index/index'));
                    //记录行为
                    action_log('update_property', 'property', $id, UID);
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Property->getError());
            }
        }
        $this->display();
    }

    //便民服务
    public function bian(){
        $m=M('Document');
        $list = $m->where("category_id=40 and status=1")->limit(0,10)->select();
        foreach ($list as $li){
            //显示数据的时候判段有没过期，过期了就删除数据；
            $time=time();

            if ($time-$li['deadline']>0){
                $id=$li['id'];

                $status=$m->where("id=$id")->field('status')->find();
                $status['status']=-1;
                $m->where("id=$id")->save($status);

            }
        }
        $this->assign('list', $list);
        if ( $p = I('get.p')){
            $data=M('Document')->where("category_id=40 and status=1")->limit($p,1)->select();
            if ($data==null){
                $this->ajaxReturn();
//                $this->ajaxReturn();
                exit;
            }
            $id=$data[0]['cover_id'];
            $da = M('Picture')->where("id=$id")->find();
            $data=[
                'data'=>$data,
                'path'=>$da['path']
            ];
            $this->ajaxReturn($data);
        }
        $this->display();
    }

    //判断用户是否登陆登陆了就显示报名
    public function bao($id){
        $list = M('DocumentArticle')->where("id=$id")->find();
        $view=M('Document');
        $vi=$view->where("id=$id")->field('view')->find();


        $vi['view']+=1;

        $view->where("id=$id")->save($vi);
        $rd = M('Document')->where("id=$id")->find();
        $username=session('user_outh')['username'];
        $this->assign('list', $list);
        $this->assign('rd', $rd);


        $this->display();
    }
}