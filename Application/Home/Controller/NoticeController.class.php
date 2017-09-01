<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/11
 * Time: 19:21
 */

namespace Home\Controller;


use Home\Model\DocumentModel;
use Think\Controller;

class NoticeController extends Controller
{
    /* 文档模型列表页 */
    public function lists($p = 1){
       // echo 4444;exit;
        /* 分类信息 */
        $category = $this->category();

        /* 获取当前分类列表 */
        $Document = D('Document');
        $list = $Document->page($p, $category['list_row'])->lists($category['id']);
        if(false === $list){
            $this->error('获取列表数据失败！');
        }

        /* 模板赋值并渲染模板 */
        $this->assign('category', $category);
        $this->assign('list', $list);

        $this->display($category['template_lists']);
    }

    public function detail($id = 0, $p = 1)
    {
        /* 标识正确性检测 */
        if(!($id && is_numeric($id))){
            $this->error('文档ID错误！');
        }

        /* 页码检测 */
        $p = intval($p);
        $p = empty($p) ? 1 : $p;

        /* 获取详细信息 */
        $Document = D('Document');
        $info = $Document->detail($id);
        if(!$info){
            $this->error($Document->getError());
        }

        /* 分类信息 */
        $category = $this->category($info['category_id']);

        /* 获取模板 */
        if(!empty($info['template'])){//已定制模板
            $tmpl = $info['template'];
        } elseif (!empty($category['template_detail'])){ //分类已定制模板
            $tmpl = $category['template_detail'];
        } else { //使用默认模板
            $tmpl = 'Article/'. get_document_model($info['model_id'],'name') .'/detail';
        }

        /* 更新浏览数 */
        $map = array('id' => $id);
        $Document->where($map)->setInc('view');

        /* 模板赋值并渲染模板 */
        $this->assign('category', $category);
        $this->assign('info', $info);
        $this->assign('page', $p); //页码
        $this->display('detail');
    }



    /* 文档分类检测 */
    private function category($id = 0){
        /* 标识正确性检测 */
        $id = $id ? $id : I('get.category', 0);
        if(empty($id)){
            $this->error('没有指定文档分类！');
        }

        /* 获取分类信息 */
        $category = D('Category')->info($id);
        if($category && 1 == $category['status']){
            switch ($category['display']) {
                case 0:
                    $this->error('该分类禁止显示！');
                    break;
                //TODO: 更多分类显示状态判断
                default:
                    return $category;
            }
        } else {
            $this->error('分类不存在或被禁用！');
        }
    }

}