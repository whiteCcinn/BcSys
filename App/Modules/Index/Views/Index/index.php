<!DOCTYPE html>
<html>

<head>

    <meta charset = "UTF-8">

    <title>HTML5环形音乐播放器演示地址</title>

    <link rel = 'stylesheet' href = 'Asset/Index/css/jquery-ui.css'>

    <style>
        @import url(Asset/Index/css/fontawesome.css);

        body {
            background: url(Asset/Index/css/006.jpg)
        }

        .music-player {
            position: absolute !important;
            top: 50%;
            left: 50%;
            margin: -207px 0 0 -207px
        }

        .music-player {
            position: relative;
            width: 415px;
            height: 415px;
            overflow: hidden;
            background: #252C36;
            border-radius: 50%;
            box-shadow: 0 1px 13px rgba(0, 0, 0, .55);
            user-select: none;
        }

        .music-player > .album {
            position: absolute;
            top: -25%;
            width: 100%;
            height: auto;
            min-height: 415px;
        }

        .music-player > .dash {
            position: absolute;
            right: 0;
            bottom: 0;
            left: 0;
            height: 300px;
            background: url(Asset/Index/css/image_png_00001.png) center bottom repeat-x;
        }

        .music-player > .dash > a {
            display: block;
            position: absolute;
            top: 50px;
            left: 12px;
            width: 24px;
            height: 24px;
            line-height: 24px;
            vertical-align: middle;
            font-size: 22px;
            text-decoration: none;
            color: #FFF;
            text-shadow: 0 1px 5px #000;
            transition: all .3s;
            opacity: .75;
        }

        .music-player > .dash > a[href="#share"] {
            left: auto;
            right: 55px;
        }

        .music-player > .dash > a[href="#love"] {
            left: auto;
            right: 18px;
        }

        .music-player > .dash > a:hover {
            opacity: 1
        }

        .music-player > .dash > a.fa-volume-off + .volume-level > em {
            display: none
        }

        .music-player > .dash > .volume-level {
            position: absolute;
            top: 57px;
            left: 45px;
            width: 40px;
            height: 6px;
            padding: 1px;
            overflow: hidden;
            border: 1px solid #FFF;
            border-radius: 3px;
            box-shadow: 0 1px 5px #000, inset 0 1px 5px rgba(0, 0, 0, .5);
            cursor: pointer;
            transition: all .3s;
            opacity: .75;
        }

        .music-player > .dash > .volume-level > em {
            display: block;
            height: 6px;
            max-width: 100%;
            background: #FFF;
            box-shadow: 0 1px 5px #000;
            border-radius: 1.5px;
        }

        .music-player > .dash > .volume-level:hover {
            opacity: 1
        }

        .music-player > .dash > .seeker {
            position: absolute;
            bottom: 7px;
            left: 50%;
            width: 400px;
            height: 203px;
            overflow: hidden;
            margin: 0 0 0 -200px;
        }

        .music-player > .dash > .seeker > .wheel {
            position: absolute;
            bottom: 0;
            width: 364px;
            height: 364px;
            border: 18px solid #201B2B;
            border-radius: 100%;
        }

        .music-player > .dash > .seeker > .wheel > .progress {
            position: absolute;
            bottom: -14px;
            left: 50%;
            width: 392px;
            height: 392px;
            overflow: hidden;
            margin: 0 0 0 -196px;
            border-radius: 100%;
            transform-origin: 50% 50%;
            background: url(Asset/Index/css/image_png_00003.png);
            transform: rotate(145deg);
        }

        .music-player > .dash > a[href="#seek"] {
            display: block;
            position: absolute;
            top: -97px;
            left: 50%;
            width: 10px;
            height: 10px;
            margin: -9px 0 0 -9px;
            background: #3A304D;
            border: 4px solid #FFF;
            border-radius: 100%;
            opacity: 1;
            transition: none;
            transform: rotate(145deg);
            transform-origin: 9px 200px;
        }

        .music-player > .dash > .controls {
            position: absolute;
            top: 85px;
            left: 50%;
            width: 180px;
            height: 55px;
            margin: 0 0 0 -90px;
            background: #201F22;
            border-radius: 23px;
        }

        .music-player > .dash > .controls:before {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 124px;
            height: 88px;
            margin: -44px 0 0 -62px;
            background: #201F22;
            border-radius: 125%;
        }

        .music-player > .dash > .controls > a[href="#play"] {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 88px;
            height: 58px;
            line-height: 58px;
            vertical-align: middle;
            text-align: center;
            text-decoration: none;
            font-size: 32px;
            color: #FFF;
            margin: -29px 0 0 -44px;
            background: #F5696C;
            border-radius: 29px;
            transition: all .3s;
        }

        .music-player > .dash > .controls > a[href="#play"]:hover {
            background: #ED484B;
            box-shadow: 0 0 5px #F5696C;
            text-shadow: 0 0 5px #FFF;
        }

        .music-player > .dash > .controls > a[href="#back"], .music-player > .dash > .controls > a[href="#forward"] {
            position: absolute;
            top: 50%;
            left: 5px;
            width: 35px;
            height: 30px;
            margin: -15px 0 0;
            line-height: 30px;
            vertical-align: middle;
            text-align: center;
            text-decoration: none;
            font-size: 22px;
            color: #BDBCBD;
            transition: all .3s;
        }

        .music-player > .dash > .controls > a[href="#forward"] {
            left: auto;
            right: 5px
        }

        .music-player > .dash > .controls > a[href="#back"]:hover, .music-player > .dash > .controls > a[href="#forward"]:hover {
            color: #FFF;
            text-shadow: 0 0 5px #BDBCBD;
        }

        .music-player > .dash > .info {
            position: absolute;
            bottom: 55px;
            left: 50%;
            width: 180px;
            margin: 0 0 0 -90px;
            text-align: center;
            font-family: Segoe UI, sans-serif;
            font-size: 12px;
            color: #FFF;
            cursor: default;
        }

        .music-player > .dash > .info > i {
            display: inline-block;
            padding: 0 10px;
            height: 20px;
            line-height: 20px;
            vertical-align: middle;
            font-size: 13px;
            font-style: normal;
            background: #201F22;
            border-radius: 10px;
        }

        .music-player > .dash > .info > label {
            display: block;
            margin: 18px 0 5px;
        }

        .music-player > .dash > .info > small {
            font-size: 10px
        }
    </style>

    <script src = "Asset/Index/js/prefixfree.min.js"></script>

</head>

<body>

<!--

Hey, you! Go check out this wonderful dribbble by Sebastian Beltz!
http://dribbble.com/shots/1408634-Music-Player

-->

<div class = "music-player">
    <img src = "Asset/Index/css/image_png_00002.png" class = "album" />
    <div class = "dash">
        <a href = "#mute" class = "fa fa-volume-up"></a>
        <span class = "volume-level">
      <em style = "width: 75%"></em>
    </span>
        <a href = "#share" class = "fa fa-share"></a>
        <a href = "#love" class = "fa fa-heart"></a>
        <div class = "seeker">
            <div class = "wheel">
                <div class = "progress"></div>
            </div>
        </div>
        <a href = "#seek"></a>
        <div class = "controls">
            <a href = "#back" class = "fa fa-fast-backward"></a>
            <a href = "#play" class = "fa fa-pause"></a>
            <a href = "#forward" class = "fa fa-fast-forward"></a>
        </div>
        <div class = "info">
            <i><span name = "current">0:00</span> / <span name = "duration">0:00</span></i>
            <label>Marteria - OMG</label>
            <small>Zum Glück in die Zukunft II</small>
        </div>
    </div>
</div>

<script src = 'Asset/Index/js/jquery_and_jqueryui.js'></script>

<script src = "Asset/Index/js/index.js"></script>
<div style = "text-align:center;clear:both">
    <script src = "/gg_bd_ad_720x90.js" type = "text/javascript"></script>
    <script src = "/follow.js" type = "text/javascript"></script>
</div>
</body>

</html>