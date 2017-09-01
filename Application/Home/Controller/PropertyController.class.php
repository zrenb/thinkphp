<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/11
 * Time: 16:01
 */

namespace Home\Controller;


class PropertyController extends HomeController
{
    public function index()
    {
        $this->display();
    }

    public function add()
    {
        if(IS_POST){

            $Property = D('Property');
            $data = $Property->create();
            $Property->sn=rand(100000,999999);
            if($data){
                $id = $Property->add();
                if($id){
                    $this->success('新增成功', U('index'));
                    //记录行为
                    action_log('update_property', 'property', $id, UID);
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Property->getError());
            }
        } else {
            $pid = I('get.pid', 0);
            //获取父导航
            if(!empty($pid)){
                $parent = M('Property')->where(array('id'=>$pid))->find();
                $this->assign('parent', $parent);
            }

            $this->assign('pid', $pid);
            $this->assign('info',null);
            $this->meta_title = '新增维修';
            $this->display('online');
        }
    }




}