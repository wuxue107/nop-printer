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
    <!-- <script src="/js/jquery.min.js"></script>-->
    <!-- <script src="/js/qrcode.min.js"></script>-->
    <!-- <script src="/js/JsBarcode.all.min.js"></script>-->
    <script>
        var errorMsg = <?=json_encode($errorMsg)?>;
        var isTpl = <?=json_encode($isTpl)?>;
        var printTpl = <?=json_encode($printTpl)?>;
        var tplParams = <?=json_encode($tplParams)?>;
        var htmlContent = <?=json_encode($htmlContent)?>;
        var pageWidth = <?=$pageWidth?>;
        var pageHeight = <?=$pageHeight?>;
    </script>
    @include('tpl-common')
    <style>
        .page{
            padding-bottom: 100px;
        }
    </style>
</head>
<body class="ticket" style='width: <?=$pageWidth ? ($pageWidth . 'px') : 'auto' ?>'>
<div id="pages">
</div>
<script type="text/javascript">
    $('#pages').html(renderTpl(errorMsg,isTpl,printTpl,tplParams,htmlContent));
    processTpl();
</script>
</body>
</html>

