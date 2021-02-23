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

var ret = 0;
helper.loadPage({
    pageUrl : pageUrl,
    timeout : timeout,
    checkCompleteJsAssert : checkCompleteJsAssert,
    onSuccess : function(page){
        if(!helper.captureElementToFile(page,element,outputFile)){
            ret = 1;
            console.error('[ERROR] 1: make image failed')
        }else{
            console.info("success");
        }
    },
    onError : function(page,msg,code){
        ret = code;
        console.error('[ERROR] ' + code + ':' + msg)
    },
    onEnd : function (page) {
        phantom.exit(ret);
    }
});
