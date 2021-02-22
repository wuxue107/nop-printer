<?php 

/**
 * @var bool $isTpl 是否为模板
 * @var string $errorMsg 是否为模板
 * @var array $tplParams 模板参数
 * @var array $printTpl 模板信息
 * @var string $htmlContent HTML打印内容
 */

$errorMsg = $errorMsg??'';
$isTpl = $isTpl??false;
$printTpl = $printTpl??null;
$tplParams = $tplParams??null;
$htmlContent = $htmlContent??'';
$pageWidth = $pageWidth??0;
$pageHeight = $pageHeight??0;

?><!DOCTYPE html>
<html lang="zh-cmn-Hans" style='font-size: 20px;'>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>票据模板</title>
    <meta charset="utf-8"/>
    <link rel="shortcut icon" href="/favicon.ico">
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <script src="/js/lodash.min.js"></script>
    <script src="/js/jquery.min.js"></script>
    <script src="/js/qrcode.min.js"></script>
    <script src="/js/JsBarcode.all.min.js"></script>
    <script>
        var errorMsg = <?=json_encode($errorMsg)?>;
        var isTpl = <?=json_encode($isTpl)?>;
        var printTpl = <?=json_encode($printTpl)?>;
        var tplParams = <?=json_encode($tplParams)?>;
        var htmlContent = <?=json_encode($htmlContent)?>;
        var pageWidth = <?=$pageWidth?>;
        var pageHeight = <?=$pageHeight?>;
    </script>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont,"Microsoft YaHei", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
            margin: 0;
        }
        .ticket {
            height: auto;
            font-size: 20px;
            line-height: 1.5em;
            margin: 0 !important;
            font-weight: 400;
        }
        .page{
            /** 小票打印，最后页尾出纸距离 **/
            padding-bottom: 10mm;
            background: white;
        }

        .ticket-58 {
            width: 370px;
        }

        .ticket-68 {
            width: 464px;
        }

        .ticket-80 {
            width: 560px;
        }

        .font-title {
            font-size: 30px;
        }

        .line_01 {
            padding: 0 20px 0;
            margin: 20px 0;
            line-height: 1px;
            border-left: 190px solid #ddd;
            border-right: 190px solid #ddd;
            text-align: center;
        }


        .text-left {
            text-align: left
        }

        .text-right {
            text-align: right
        }

        .text-center {
            text-align: center
        }

        .text-justify {
            text-align: justify
        }

        .text-nowrap {
            white-space: nowrap
        }

        .text-lowercase {
            text-transform: lowercase
        }

        .text-uppercase {
            text-transform: uppercase
        }

        .text-capitalize {
            text-transform: capitalize
        }
    </style>
</head>
<body class="ticket" style='width: <?=$pageWidth ? ($pageWidth . 'px') : 'auto' ?>'>
<div id="page" class="page">
</div>
<script type="text/javascript">
    function processHtml(html) {
        var el = $('#page')
        el.html(html);

        var defaultQrcodeOption = {
            text: "",
            width: 128,
            height: 128,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        };
        el.find('.qrcode').each(function(){
            var item = $(this);
            var userOption = item.data();
            var option = $.extend({},defaultQrcodeOption,userOption);
            new QRCode(this, option);
        });
        
        var defaultBarcodeOption = {
            height: 35,
            displayValue: true
        };

        el.find('.barcode').each(function(){
            var item = $(this);
            var t = $('<canvas />').appendTo(item);
            var userOption = item.data();
            var option = $.extend({},defaultBarcodeOption,userOption);
            JsBarcode(t.get(0), option.text, option);
        })
    }

    if(errorMsg){
        processHtml("<h4>"+errorMsg+"</h4>");
    }else if(!isTpl){
        processHtml(htmlContent);
    }else{
        try{
            var html = _.template(printTpl.tpl_content)(tplParams);
            processHtml(html);
        }catch (e) {
            console.log(e);
            processHtml("<h4>渲染模板错误："+e.toString()+"</h4>");
        }
    }
</script>
</body>
</html>

