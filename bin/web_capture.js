/**
 * Created by admin on 2019/3/10.
 */
var system = require('system');

if (system.args.length !== 2) {
    console.log('Usage: phantomjs WEB_URL SAVE_FILE');
    phantom.exit();
}

var webUrl = system.args[1],
    saveFile = system.args[2],
    width = ~~ system.args[3],
    height = ~~ system.args[4],
    timeout = system.args[5];

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
var viewSize = { width: width};
if(height){
    viewSize.height = height;
}

page.viewportSize = viewSize;
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
            page.render(saveFile);
            phantom.exit();
        }
    },50);
});
