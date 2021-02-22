/**
 * Created by admin on 2019/3/10.
 */
var system = require('system');

if (system.args.length < 2) {
    console.log('Usage: phantomjs WEB_URL SAVE_FILE [ELEMENT_SELECTOR] [TIMEOUT]');
    phantom.exit();
}

var webUrl = system.args[1],
    saveFile = system.args[2],
    element = system.args[3];
    timeout = system.args[4];

if(!element){
    element = 'body';
}

if(timeout === undefined){
    timeout = 15000;
}else{
    timeout = ~~timeout;
}
console.log("load url:" + webUrl);

setTimeout(function () {
    // 超时未渲染完成则退出
    console.log("wait render timeout:" + webUrl);
    phantom.exit();
}, timeout);

var page = require('webpage').create();

page.onConsoleMessage = function(msg, lineNum, sourceId) {
    console.log("CONSOLE:["+sourceId+ ":" +lineNum+"] " + msg);
};

// page.onResourceRequested = function(request) {
//     console.log('Request ' + request.url);
// };
// page.onResourceReceived = function(response) {
//     console.log('Receive ' + response.statusText + '|' + response.contentType + '|' + response.url);
// };
var checkComplete = function(){
    return page.evaluate(function(){
        return true;
        // 实现页面渲染完毕的检查点，可以在网页内部做渲染完毕检测，设置标志位
        // return window.mapImageLoadOk;
    });
};

page.open(webUrl, function (status) {
    console.log("Status: " + status);
    if(status !== "success") {
        console.log('FAIL to load the address');
        phantom.exit();
    }
    // 加载外部JS
    // page.includeJs('http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js', function() {
    //
    // });

    setInterval(function(){
        if(true === checkComplete()){
            try{
                var bb = page.evaluate(function (element) {
                    console.log(element);
                    console.log(document.querySelector.toString());
                    return document.querySelector(element).getBoundingClientRect();
                });
                // 按照实际页面的高度，设定渲染的宽高
                page.clipRect = {
                    top:    bb.top,
                    left:   bb.left,
                    width:  bb.width,
                    height: bb.height
                };

                page.render(saveFile);
            }catch (e) {
                console.error(e.toString());
            }
            
            phantom.exit();
        }
    },50);
});
