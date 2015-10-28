<?php
/**
 *
 * 音乐搜索器 - 入口
 *
 * @author     MaiCong <i@maicong.me>
 * @date  2015-06-15 18:14:52
 * @version    1.0.4
 *
 */

define('MC_CORE', true);

// SoundCloud 客户端 ID，如果失效请更改
define('MC_SC_CLIENT_ID', 'b45b1aa10f1ac2941910a7f0d10f8e28');

// Curl 代理地址，解决翻墙问题。例如：define('MC_PROXY', 'http://10.10.10.10:8123');
define('MC_PROXY', false);

require_once dirname(__FILE__).'/music.php';

if (ajax_post('music_input') && ajax_post('music_filter')) {
    $music_input      = ajax_post('music_input');
    $music_filter     = ajax_post('music_filter');
    $music_type       = ajax_post('music_type');
    $music_type_allow = array('163', '1ting', 'baidu', 'kugou', 'kuwo', 'qq', 'xiami', '5sing', 'ttpod', 'migu', 'soundcloud');
    $music_name       = null;
    $music_id         = null;
    $music_url        = null;
    switch ($music_filter) {
        case 'name':
            $music_valid      = preg_match('/^.+?$/isu', $music_input);
            $music_name       = $music_input;
            $music_type_valid = in_array($music_type, $music_type_allow, true);
            break;
        case 'id':
            $music_valid      = preg_match('/^[\w\/]+$/is', $music_input);
            $music_type_valid = in_array($music_type, $music_type_allow, true);
            $music_id         = $music_input;
            break;
        case 'url':
            $music_valid      = preg_match('/^(http|https|ftp):\/\/{1}([\S]+)$/is', $music_input);
            $music_type_valid = true;
            $music_url        = $music_input;
            break;
        default:
            $music_valid = false;
            break;
    }
    if ($music_valid && $music_type_valid) {
        if (null !== $music_name) {
            $music_name     = htmlspecialchars($music_name, ENT_QUOTES, 'UTF-8');
            $music_response = maicong_get_song_by_name($music_name, $music_type);
        }
        if (null !== $music_id) {
            $music_id       = htmlspecialchars($music_id, ENT_QUOTES, 'UTF-8');
            $music_response = maicong_get_song_by_id($music_id, $music_type);
        }
        if (null !== $music_url) {
            $music_response = maicong_get_song_by_url($music_url);
        }
        if (!empty($music_response)) {
            $reinfo = array('status' => '200', 'msg' => '', 'data' => $music_response);
        } else {
            $reinfo = array('status' => '0', 'msg' => 'ㄟ( ▔, ▔ )ㄏ，没有找到相关信息');
        }
    } else {
        $reinfo = array('status' => '0', 'msg' => '(・-・*)，请检查您的输入是否正确');
    }
    header('Content-type:text/json');
    echo json_encode($reinfo);
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>音乐搜索器</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Cache-Control" content="no-transform">
    <meta http-equiv="Cache-Control" content="no-siteapp">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="author" content="Maicong.me">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="音乐搜索器">
    <meta name="application-name" content="音乐搜索器">
    <meta name="format-detection" content="telephone=no">
    <link rel="shortcut icon" href="static/favicon.ico">
    <link rel="apple-touch-icon" href="static/apple-touch-icon.png">
    <link rel="canonical" href="http://music.2333.me/">
    <link rel="stylesheet" href="http://cdn.amazeui.org/amazeui/2.3.0/css/amazeui.min.css">
    <link rel="stylesheet" href="static/style.css">
</head>
<body>
    <header class="am-topbar am-topbar-fixed-top">
        <div class="am-container">
            <h1 class="am-topbar-brand">
                <a href="/">超能实验室</a>
            </h1>
        </div>
    </header>
    <section class="am-g about">
        <div class="am-container am-margin-vertical-xl">
            <header class="am-padding-vertical">
                <h2 class="about-title about-color">音乐搜索器</h2>
                <p class="am-text-center">特制网易一听百度酷狗酷我QQ虾米5sing天天动听咪咕SoundCloud音乐搜索解决方案</p>
            </header>
            <hr>
            <div class="am-u-lg-12 am-padding-vertical">
                <form class="am-form am-margin-bottom-lg" method="post" id="form-vld">
                    <div class="am-u-md-12 am-u-sm-centered">
                        <ul id="form-tabs" class="am-nav am-nav-pills am-nav-justify am-margin-bottom music-tabs">
                            <li class="am-active" data-filter="name"><a>音乐名称</a></li>
                            <li data-filter="id"><a>音乐ID</a></li>
                            <li data-filter="url"><a>音乐地址</a></li>
                        </ul>
                        <div class="am-form-group">
                            <input type="text" id="music_input" data-filter="name" class="am-form-field am-input-lg am-text-center am-radius" minlength="1" placeholder="例如: 不要说话 陈奕迅" data-am-loading="{loadingText: ' '}" pattern="^.+?$" required>
                            <div class="am-alert am-alert-danger am-animation-shake"></div>
                        </div>
                        <div class="am-form-group am-text-center music-type">
                            <label class="am-radio-inline">
                                <input type="radio" name="music_type" value="163" data-am-ucheck checked> 网易
                            </label>
                            <label class="am-radio-inline">
                                <input type="radio" name="music_type" value="1ting" data-am-ucheck> 一听
                            </label>
                            <label class="am-radio-inline">
                                <input type="radio" name="music_type" value="baidu" data-am-ucheck> 百度
                            </label>
                            <label class="am-radio-inline">
                                <input type="radio" name="music_type" value="kugou" data-am-ucheck> 酷狗
                            </label>
                            <label class="am-radio-inline">
                                <input type="radio" name="music_type" value="kuwo" data-am-ucheck> 酷我
                            </label>
                            <label class="am-radio-inline">
                                <input type="radio" name="music_type" value="qq" data-am-ucheck> ＱＱ
                            </label>
                            <label class="am-radio-inline">
                                <input type="radio" name="music_type" value="xiami" data-am-ucheck> 虾米
                            </label>
                            <label class="am-radio-inline">
                                <input type="radio" name="music_type" value="5sing" data-am-ucheck> 5sing
                            </label>
                            <label class="am-radio-inline">
                                <input type="radio" name="music_type" value="ttpod" data-am-ucheck> 天天动听
                            </label>
                            <label class="am-radio-inline">
                                <input type="radio" name="music_type" value="migu" data-am-ucheck> 咪咕
                            </label>
                            <label class="am-radio-inline">
                                <input type="radio" name="music_type" value="soundcloud" data-am-ucheck> SoundCloud
                            </label>
                        </div>
                        <button type="submit" id="submit" class="am-btn am-btn-primary am-btn-lg am-btn-block am-radius" data-am-loading="{spinner: 'cog', loadingText: '正在搜索相关音乐...', resetText: 'Get &#x221A;'}">Get &#x221A;</button>
                    </div>
                </form>
                <form class="am-form am-u-md-12 am-u-sm-centered music-main">
                    <a type="button" id="getit" class="am-btn am-btn-success am-btn-lg am-btn-block am-radius am-margin-bottom-lg">成功 Get &#x221A; 返回继续 <i class="am-icon-reply am-icon-fw"></i></a>

                    <a id="current-music-donwload-btn" class="am-btn am-btn-primary am-btn-lg am-radius am-btn-block" download href="#">下载 MP3 文件</a>
<br>
                    <div class="am-input-group am-input-group-sm am-margin-bottom-sm" data-am-popover="{content: '音乐链接', trigger: 'hover'}">
                        <span class="am-input-group-label"><i class="am-icon-music am-icon-fw"></i></span>
                        <input type="text" id="music-src" class="am-form-field">
                    </div>
                    <div class="am-g">
                        <div class="am-u-lg-6">
                            <div class="am-input-group am-input-group-sm am-margin-bottom-sm" data-am-popover="{content: '音乐名称', trigger: 'hover'}">
                                <span class="am-input-group-label"><i class="am-icon-tag am-icon-fw"></i></span>
                                <input type="text" id="music-name" class="am-form-field">
                            </div>
                        </div>
                        <div class="am-u-lg-6">
                            <div class="am-input-group am-input-group-sm am-margin-bottom-sm" data-am-popover="{content: '音乐作者', trigger: 'hover'}">
                                <span class="am-input-group-label"><i class="am-icon-user am-icon-fw"></i></span>
                                <input type="text" id="music-author" class="am-form-field">
                            </div>
                        </div>
                    </div>
                    <div id="music-show" class="am-margin-vertical"></div>
                </form>
                <div class="am-u-md-12 am-u-sm-centered am-margin-vertical music-tips" style="display:none">
                    <h4>帮助：</h4>
                    <p><b>标红</b> 为 <strong>音乐ID</strong>，<u>下划线</u> 表示 <strong>音乐地址</strong></p>
                    <p><span>网易：</span><u>http://music.163.com/#/song?id=<b>25906124</b></u></p>
                    <p><span>一听：</span><u>http://www.1ting.com/player/b6/player_<b>220513</b>.html</u></p>
                    <p><span>百度：</span><u>http://music.baidu.com/song/<b>556113</b></u></p>
                    <p><span>酷狗：</span><u>http://m.kugou.com/play/info/<b>08228af3cb404e8a4e7e9871bf543ff6</b></u></p>
                    <p><span>酷我：</span><u>http://www.kuwo.cn/yinyue/<b>382425</b>/</u></p>
                    <p><span>ＱＱ：</span><u>http://y.qq.com/#type=song&amp;mid=<b>002B2EAA3brD5b</b>&amp;play=0</u></p>
                    <p><span>虾米：</span><u>http://www.xiami.com/song/<b>2113248</b></u></p>
                    <p><span>5sing：</span><u>http://5sing.kugou.com/<b>fc/2277364</b>.html</u></p>
                    <p><span>天天动听：</span><u>http://m.ttpod.com/#a=gqxq&amp;from=ss&amp;neid=<b>1029409</b>&amp;singerid=...</u></p>
                    <p><span>咪咕：</span><u>http://music.migu.cn/#/song/<b>477803</b>/P7Z1Y1L1N1/1/001002C</u></p>
                    <p><span>SoundCloud (ID)：</span><u>soundcloud://sounds:<b>197401418</b></u> (请查看源码)</p>
                    <p><span>SoundCloud (地址)：</span><u>https://soundcloud.com/user2953945/tr-n-d-ch-t-n-eason-chan-kh-ng</u></p>
                    <div class="more">查看更多</div>
                </div>
            </div>
        </div>
        
    </section>
    <footer class="footer am-topbar-fixed-bottom">
        <p>Made With <i class="am-icon-heartbeat" style="color:#F74343;font-size:1.5em;"></i> By The EST Group. </p>
    </footer>
    <script src="http://libs.baidu.com/jquery/1.11.1/jquery.min.js"></script>
    <script src="http://cdn.amazeui.org/amazeui/2.3.0/js/amazeui.min.js"></script>
    <script src="static/music.js"></script>
</body>
</html>