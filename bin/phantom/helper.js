// This is to be used by "module.js" (and "module.coffee") example(s).
// There should NOT be a "universe.coffee" as only 1 of the 2 would
//  ever be loaded unless the file extension was specified.

"use strict";


var WebPage = require('webpage');

/**
 * 去两边空格
 * @return {string}
 */
var trim  = function() {
    return this.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
};

/**
 * 转驼峰
 * @param str
 * @return {string}
 */
var toCamel = function(str) {
    return str.replace(/([^_\s\-])(?:[_\s-]+([^_\s-]))/g, function ($0, $1, $2) {
        return $1 + $2.toUpperCase();
    });
};

/**
 * 解析命令行参数
 */
var argsParser = function(optionArgs){
    if(optionArgs === undefined){
        optionArgs = require('system').args.slice(1)
    }
    var options = {
        args : [],
    };

    var index = 1;
    for(var i in optionArgs){
        var optionArg = optionArgs[i];
        var matchs = optionArg.match(/^--([\w-]+)(=(.*))?$/);
        if(!matchs){
            options.args[index] = optionArg;
            index++;
        }else{
            var optionName = toCamel(matchs[1]);
            options[optionName] = matchs[3]
        }
    }
    options._transValue = function(ret,defaultValue){
        if(ret === undefined || ret === ""){
            return defaultValue;
        }
        if(defaultValue === true || defaultValue === false){
            if(ret === "0" || ret === "false" || ret === "no"){
                return false;
            }
            return true;
        }

        if(typeof defaultValue === 'number'){
            return ret - 0;
        }

        return ret;
    };
    options.getOption = function(name,defaultValue){
        return options._transValue(options[toCamel(name)],defaultValue);
    };
    
    options.getArgs = function (index,defaultValue) {
        return options._transValue(options.args[index],defaultValue);
    };

    return options;
};

var captureElement = function (page,elementSelector) {
    var bb = page.evaluate(function (elementSelector) {
        return document.querySelector(elementSelector).getBoundingClientRect();
    },elementSelector);
    // 按照实际页面的高度，设定渲染的宽高
    page.clipRect = {
        top:    bb.top,
        left:   bb.left,
        width:  bb.width,
        height: bb.height
    };

    return page;
};

var captureElementToFile = function (page,elementSelector,outputFile) {
    var oldRect = page.clipRect;
    captureElement(page,elementSelector).render(outputFile);
    page.clipRect = oldRect;
    return require('fs').exists(path);
};

var captureElementToBase64 = function (page,elementSelector) {
    var oldRect = page.clipRect;
    var ret = 'data:image/png;base64,' + captureElement(page,elementSelector).renderBase64('png');
    page.clipRect = oldRect;
    return ret;
};

var noop = function () {};


var loadPage = function(userOption){
    var option;
    var intervalTickId;
    var timeoutTickId;
    var page;
    var defaultOption = {
        width : 1000,
        height : 1000,
        debug : true,
        pageUrl : '',
        interval : 50,
        timeout : 15000,
        checkCompleteJsAssert : 'true',
        onError : noop,
        onSuccess : noop, // onComplete(page)
        onEnd : noop //// onEnd(page)
    };
    
    option = extend(true,defaultOption,userOption);

    console.log(JSON.stringify(option));
    
    var pageError = function (msg,code) {
        code = code || 99;

        
        option.onError(page,msg,code);
        pageEnd(page)
    };
    
    var pageEnd = function (page) {
        try{
            option.onEnd(page);
        }catch (e) {
            console.error(e.toString())
        }
        
        try{
            if(page){
                page.close();
            }
        }catch (e) {
        }
    };
    if(!option.pageUrl){
        return pageError("[ERROR]:" + "pageUrl is required .",2)
    }

    page = WebPage.create();
    page.viewportSize = {width:option.width,height:option.height};

    timeoutTickId = setTimeout(function () {
        option.onEnd(page);
        // 超时未渲染完成则退出
        return pageError("wait render timeout:" + option.timeout + 'ms',3)
    }, option.timeout + option.interval + 5);

    page.onPrompt = function(msg, defaultVal) {
        return defaultVal;
    };
    page.onAlert = function(msg) {
        console.info('[ALERT]: ' + msg);
    };
    page.onConfirm = function(msg) {
        console.log('[CONFIRM]: ' + msg);
        return true; // `true` === pressing the "OK" button, `false` === pressing the "Cancel" button
    };
    
    if(option.debug){
         page.onConsoleMessage = function(msg, lineNum, sourceId) {
            console.log("CONSOLE:["+sourceId+ ":" +lineNum+"] " + msg);
         };

        page.onResourceError = function(resourceError) {
            console.log('Unable to load resource (#' + resourceError.id + 'URL:' + resourceError.url + ')');
            console.log('Error code: ' + resourceError.errorCode + '. Description: ' + resourceError.errorString);
        };
    }
    
    var checkComplete = function(){
        return page.evaluate(function(checkCompleteJsAssert){
            return eval(checkCompleteJsAssert);
        },option.checkCompleteJsAssert);
    };

    try{
        console.info("open url: " + option.pageUrl);
        page.open(option.pageUrl, function (status) {
            console.info("Status: " + status);
            if(status !== "success") {
                return pageError('failed to load the address',4);
            }

            // 加载外部JS
            // page.includeJs('https://cdn.bootcdn.net/ajax/libs/jquery/2.1.4/jquery.min.js', function() {
            //
            // });

            var checkOk = false;
            var intervalTickId = setInterval(function(){
                if(checkOk){
                    clearInterval(intervalTickId)
                    if(timeoutTickId){
                        clearTimeout(timeoutTickId);
                    }

                    try{
                        option.onSuccess(page);
                        pageEnd(page);
                    }catch (e) {
                        return pageError("[ERROR]:" + e.toString(),5);
                    }
                }else{
                    checkOk = checkComplete();
                }
            },option.interval);
        });
    }catch (e) {
        return pageError("[ERROR]:" + e.toString(),6)
    }
};

var isArray = Array.isArray;
var isFunction = function (obj) {
    return typeof obj === 'function';
};

var isPlainObject = function(obj) {
    "use strict";
    if (!obj || typeof(obj) !== 'object')
        return false;
    var type = Object.prototype.toString.call(obj).match(/^\[object\s(.*)\]$/)[1].toLowerCase();
    return (type === 'object');
};


var extend = function() {
    var options, name, src, copy, copyIsArray, clone,
        target = arguments[0] || {},
        i = 1,
        length = arguments.length,
        deep = false;

    // Handle a deep copy situation
    if ( typeof target === "boolean" ) {
        deep = target;

        // Skip the boolean and the target
        target = arguments[ i ] || {};
        i++;
    }

    // Handle case when target is a string or something (possible in deep copy)
    if ( typeof target !== "object" && !isFunction(target) ) {
        target = {};
    }

    // Extend jQuery itself if only one argument is passed
    if ( i === length ) {
        target = this;
        i--;
    }

    for ( ; i < length; i++ ) {
        // Only deal with non-null/undefined values
        if ( (options = arguments[ i ]) != null ) {
            // Extend the base object
            for ( name in options ) {
                src = target[ name ];
                copy = options[ name ];

                // Prevent never-ending loop
                if ( target === copy ) {
                    continue;
                }

                // Recurse if we're merging plain objects or arrays
                if ( deep && copy && ( isPlainObject(copy) || (copyIsArray = isArray(copy)) ) ) {
                    if ( copyIsArray ) {
                        copyIsArray = false;
                        clone = src && isArray(src) ? src : [];

                    } else {
                        clone = src && isPlainObject(src) ? src : {};
                    }

                    // Never move original objects, clone them
                    target[ name ] = extend( deep, clone, copy );

                    // Don't bring in undefined values
                } else if ( copy !== undefined ) {
                    target[ name ] = copy;
                }
            }
        }
    }

    // Return the modified object
    return target;
};

exports.trim = trim;
exports.toCamel = toCamel;
exports.argsParser = argsParser;
exports.captureElement = captureElement;
exports.captureElementToBase64 = captureElementToBase64;
exports.captureElementToFile = captureElementToFile;
exports.isArray = isArray;
exports.isFunction = isFunction;
exports.extend = extend;
exports.loadPage = loadPage;
exports.isPlainObject = isPlainObject;


