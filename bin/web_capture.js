/**
 * 
 * 页面元素，快照保存图片
 * 
 * phantomjs [--timeout=15000] [--element=body] [--check-complete-js-assert=true] PAGE_URL OUTPUT_FILE
 * 
 * @refer https://phantomjs.org/api/command-line.html
 */
var helper = require('./phantom/helper.js');

var command = helper.argsParser();

var pageUrl = command.getArgs(1),
    outputFile = command.getArgs(2,'output.png'),
    element = command.getOption('element','body'),
    timeout = command.getOption('timeout',15000),
    checkCompleteJsAssert = command.getOption('checkCompleteJsAssert','true');

if(!pageUrl){
    console.error("param PAGE_URL is required");
    phantom.exit();
}

if(!outputFile){
    console.error("param OUTPUT_FILE is required")
    phantom.exit();
}

console.info("PAGE_URL: " + pageUrl);
console.info("OUTPUT_FILE: " + outputFile);

setTimeout(function () {
    // 超时未渲染完成则退出
    console.info("wait render timeout:" + timeout + 'ms');
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

page.open(pageUrl, function (status) {
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

                page.render(outputFile);
            }catch (e) {
                console.error(e.toString());
            }
            
            phantom.exit();
        }
    },50);
});
