<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/14
 * Time: 16:53
 */

namespace Home\Controller;
use EasyWeChat\Message\News;
//use EasyWeChat\Foundation\Application;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Message\Text;
use Think\Controller;

require "./vendor/autoload.php";        // 引入 composer 入口文件 从入口文件开始
class WechatController extends Controller
{   //服务端验证
    public function index(){
        //echo "<pre>";
       // var_dump($_SERVER);exit;
        if($_SERVER['REQUEST_METHOD']=='POST'){
            $app = new Application(C('wechat_config'));
            // ... 前面部分省略
            $server = $app->server;
            $server->setMessageHandler(function ($message) {
                //return $message->ToUserName."==".$message->FromUserName  ;
                /*
                 * 请求消息基本属性(以下所有消息都有的基本属性)：
                 *  $message->ToUserName    接收方帐号（该公众号 ID）
                 *   $message->FromUserName  发送方帐号（OpenID, 代表用户的唯一标识）
                 *  $message->CreateTime    消息创建时间（时间戳）
                 *   $message->MsgId         消息 ID（64位整型）
                 *
                 */
               switch ($message->MsgType) {
                    case 'event':

                        switch ($message->Event) {
                            case 'subscribe':
                                return '欢迎关注我们平台';
                                break;
                            case 'unsubscribe':
                                # code...
                                break;
                            case 'CLICK ':      //自定义点击事件
                                return $message->EventKey;
                                break;
                        }

                        //return '收到事件消息';
                        break;
                    case 'text':
                        //使用对象的方式处理文本消息
                        $content = $message->Content;
                        if($content){
                            preg_match("/^(\w)(.*)$/",$content,$matches);
                            switch ($matches[1]){
                                case 'H'://基于位置的搜索
                                    $query = urlencode($matches[2]);//转义
                                    //从数据库中查询出对应open_id的坐标
                                    $user_location = M('location')->where(['open_id'=>$message->FromUserName])->find();
                                    if(empty($user_location)){
                                        return "请先发送你的位置！";
                                    }
                                    $location = $user_location['x'].','.$user_location['y'];
                                    $search_url = "http://api.map.baidu.com/place/search?query={$query}&location={$location}&radius=1000&output=xml";
                                    //解析xml
                                    $simpleXml = simplexml_load_file($search_url);
//                dump($simpleXml);
                                    $news = [];//所有的图文消息
                                    $news_count = 0;
                                    foreach ($simpleXml->results->result as $k=>$v){
                                        /**
                                         * 'title'       => $title,
                                        'description' => '...',
                                        'url'         => $url,
                                        'image'       => $image,
                                         */
                                        $url = html_entity_decode($v->detail_url);//将url中的实体符号转换回来
                                        $lng = (string)$v->location->lng;
                                        $lat = (string)$v->location->lat;
                                        //获取百度静态图片
                                        $image_url = "http://api.map.baidu.com/panorama/v2?ak=mzyIoPg42h4yy9Twcvcy9t0oWlvlTbhx&width=512&height=256&location={$lng},{$lat}&fov=180";
                                        $new = new News(['title'=>(string)$v->name,'description'=>(string)$v->address,'url'=>$url,'image'=>$image_url]);
                                        $news[] = $new;
                                        $news_count++;
                                        if($news_count >= 8){
                                            break;
                                        }
                                    }
                                    return $news;
                                    break;
                                case ''://搜索天气
                                    $simplexml=simplexml_load_file('http://flash.weather.com.cn/wmaps/xml/sichuan.xml');
                                    $weather=[];

                                    foreach ($simplexml as $name=>$value){

                                        $weather[(string)$value['cityname']]=(string)$value['stateDetailed'];

                                    }
                                    if(isset($weather[$content])){
                                       return $weather[$content];
                                    }else{
                                        return "你换个方式，或许我就懂了哦！（例如：成都或H酒店）";
                                    }
                                    break;
                            }

                        }else{

                            return 222;
                        }
                        /*$text = new Text(['content' => '您好！overtrue。']);
                        return $text;*/
                        break;
                    case 'image':
                        //图文消息
                        $news1 = new News([
                            'title'       => '我的图文消息AAA',
                            'description' => '...',
                            'url'         => 'http://119.23.229.180/index.php?s=/Home/member/index.html',
                            'image'       => 'http://pic31.photophoto.cn/20140417/0036036332651295_b.jpg',
                            // ...
                        ]);
                        $news2 = new News([
                        'title'       => '我的图文消息BBB',
                        'description' => '...',
                        'url'         => 'http://119.23.229.180/index.php?s=/Home/Index/index.html',
                        'image'       => 'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1502728627161&di=5f51df5e8bb6c0d65c49e9a871d868fe&imgtype=0&src=http%3A%2F%2Fimg1d.xgo-img.com.cn%2Fpics%2F157%2F156499.jpg',
                        // ...
                    ]);
                        $news3 = new News([
                            'title'       => '我的图文消息CCC',
                            'description' => '...',
                            'url'         => 'http://119.23.229.180/index.php?s=/Home/Index/index.html',
                            'image'       => 'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1502728627160&di=4a1a0a2c29cef4bf1222bd2336ac6a5b&imgtype=0&src=http%3A%2F%2Fimg1a.xgo-img.com.cn%2Fpics%2F1538%2Fa1537491.jpg',
                            // ...
                        ]);
                        return [$news1,$news2,$news3];
                        break;
                    case 'voice':
                        return '收到语音消息';
                        break;
                    case 'video':
                        return '收到视频消息';
                        break;
                    case 'location':
                        /*$message->MsgType     location
                            $message->Location_X  地理位置纬度
                            $message->Location_Y  地理位置经度
                            $message->Scale       地图缩放大小
                            $message->Label       地理位置信息
                        */
                        $sql = "insert into onethink_location(open_id,x,y,scale,label) VALUES ('{$message->FromUserName}','$message->Location_X','$message->Location_Y','{$message->Scale}','{$message->Label}') ON  DUPLICATE KEY UPDATE x='{$message->Location_X}',y='{$message->Location_Y}',scale='{$message->Scale}',label='{$message->Label}'";
                        M()->execute($sql);
                        return '回复例如：s酒店/l成都！可查附近酒店或地方天气'  ;
                        break;
                    case 'link':
                        return '收到链接消息';
                        break;
                    // ... 其它消息
                    default:
                        return '收到其它消息';
                        break;
                }
                // ...
            });
            $server->serve()->send();
        }
    }



    //添加菜单
    public function menu(){
        // ...
        $app = new Application(C('wechat_config'));
        $menu = $app->menu;

        $buttons = [
            [
                "name"       => "导航",
                "sub_button" =>
                [
                    [
                        "type" => "view",
                        "name" => "剁手号",
                        "url"  => "http://www.soso.com/"
                    ],
                    [
                        "type" => "view",
                        "name" => "首页",
                        "url"  => "http://v.qq.com/"
                    ],

                ],
            ],
            [
                "name"       => "菜单",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "天猫",
                        "url"  => "https://www.tmall.com/"
                    ],
                    [
                        "type" => "view",
                        "name" => "京东",
                        "url"  => "https://www.jd.com/"
                    ],
                    [
                        "type" => "view",
                        "name" => "小区租售",
                        "url"  => "http://v.qq.com/"
                    ],
                    [
                        "type" => "view",
                        "name" => "唯品会",
                        "url" => "https://www.vip.com/"
                    ],
                    [
                        "type" => "view",
                        "name" => "聚美优品",
                        "url" => "http://bj.jumei.com/"
                    ],
                ],
            ],
            [
                "type"=>"view",
                "name"=>"个人中心",
                "url"  => "http://wxzjm.mini7z.top/index.php?s=/Home/member/index.html"
            ]
        ];
        $menu->add($buttons);
        $menus = $menu->all();
        var_dump($menus);
    }


    //发起授权方法
    public static function getAccess(){

        if(!session('open_id')){
            //var_dump($_SERVER);exit;
            $app=new Application(C('wechat_config'));
            //发起授权  snsapi_userinfo snsapi_base
            $response = $app->oauth->scopes(['snsapi_base'])
                ->redirect();
            //将请求的路由保存到session中
            session('request_url',$_SERVER['PATH_INFO']);
            $response->send(); // Laravel 里请使用：return $response;
        }
    }


    //授权的回调页面
    public function back(){
        //获取已授权用户
        $app=new Application(C('wechat_config'));
        $user = $app->oauth->user();
        //var_dump($user);
        // $user 可以用的方法:
        // $user->getId();  // 对应微信的 OPENID
        // $user->getNickname(); // 对应微信的 nickname
        // $user->getName(); // 对应微信的 nickname
        // $user->getAvatar(); // 头像网址
        // $user->getOriginal(); // 原始API返回的结果
        // $user->getToken(); // access_token， 比如用于地址共享时使用
        //保存用户的openID
        session('open_id',$user->getId());
        $this->redirect(session('request_url'));
    }

    public function sc(){

        $simplexml=simplexml_load_file('http://flash.weather.com.cn/wmaps/xml/sichuan.xml');
        var_dump($simplexml);
        $weather=[];

        foreach ($simplexml as $name=>$value){

            var_dump($value);
            $weather[(string)$value['cityname']]=(string)$value['stateDetailed'];

        }

    }
}