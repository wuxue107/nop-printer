/**
 * 
 * 页面元素，快照保存图片
 * 
 * 
 * @refer https://phantomjs.org/api/command-line.html
 */
var system = require('system');

if (system.args.length < 2) {
    console.info('Usage: phantomjs WEB_URL SAVE_FILE [ELEMENT_SELECTOR] [TIMEOUT] [checkCompleteJsAssert]');
    phantom.exit();
}

var webUrl = system.args[1],
    saveFile = system.args[2],
    element = system.args[3],
    timeout = system.args[4],
    checkCompleteJsAssert = system.args[5];

// 实现页面渲染完毕的检查点，可以在网页内部做渲染完毕检测，设置标志位
if(checkCompleteJsAssert === undefined || checkCompleteJsAssert === ""){
    checkCompleteJsAssert = "true";
}

if(!element){
    element = 'body';
}

if(timeout === undefined){
    timeout = 15000;
}else{
    timeout = ~~timeout;
}
console.info("load url:" + webUrl);

setTimeout(function () {
    // 超时未渲染完成则退出
    console.info("wait render timeout:" + webUrl);
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
    return page.evaluate(function(checkCompleteJsAssert){
        return eval(checkCompleteJsAssert);
    },checkCompleteJsAssert);
};

page.open(webUrl, function (status) {
    console.info("Status: " + status);
    if(status !== "success") {
        console.error('FAIL to load the address');
        phantom.exit();
    }
    // 加载外部JS
    // page.includeJs('https://cdn.bootcdn.net/ajax/libs/jquery/2.1.4/jquery.min.js', function() {
    //
    // });

    var tickId = setInterval(function(){
        if(true === checkComplete()){
            clearInterval(tickId);
            try{
                var bb = page.evaluate(function (element) {
                    return document.querySelector(element).getBoundingClientRect();
                },element);
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
