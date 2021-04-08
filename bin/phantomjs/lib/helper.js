// This is to be used by "module.js" (and "module.coffee") example(s).
// There should NOT be a "universe.coffee" as only 1 of the 2 would
//  ever be loaded unless the file extension was specified.

"use strict";


var WebPage = require('webpage');


var noop = function () {};

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
        _args : [],
    };

    var index = 1;
    for(var i in optionArgs){
        var optionArg = optionArgs[i];
        var matchs = optionArg.match(/^--([\w-]+)(=(.*))?$/);
        if(!matchs){
            options._args[index] = optionArg;
            index++;
        }else{
            var optionName = toCamel(matchs[1]);
            options[optionName] = matchs[3] === undefined ? true : matchs[3];
        }
    }

    options._transValue = function(map,name,defaultValue){
        var isBoolean = defaultValue === true || defaultValue === false;
        var ret = map[name];

        if(ret === undefined || ret === ""){
            return defaultValue;
        }

        if(isBoolean){
            if(ret === "0" || ret === "false" || ret === "no" || ret === undefined){
                return false;
            }

            return true;
        }

        if(typeof defaultValue === 'number'){
            return ret - 0;
        }

        return '' + ret;
    };
    
    options.getOption = function(name,defaultValue){
        return options._transValue(options,toCamel(name),defaultValue);
    };
    
    options.getArgs = function (index,defaultValue) {
        return options._transValue(options._args,index,defaultValue);
    };

    return options;
};


var getElementRect = function(page,elementSelector){
    var bb = page.evaluate(function (elementSelector) {
        return document.querySelector(elementSelector).getBoundingClientRect();
    },elementSelector);

    return {
        top:    bb.top,
        left:   bb.left,
        width:  bb.width,
        height: bb.height
    }
};

/**
 * 
 * @param page
 * @param elementSelectors
 * @returns object 
 * PS: {
 *     count : 1,
 *     rects : {
 *         "body" : [
 *             {
 *                 top:    0,
 *                 left:   0,
 *                 width:  600,
 *                 height: 800
 *             }
 *         ]
 *     }
 * }
 */
var getElementsRect = function(page,elementSelectors){
    if(!isArray(elementSelectors)){
        elementSelectors = [elementSelectors]
    }
    var ret = page.evaluate(function (elementSelectors) {
        var rects = {};
        var count = 0;
        for(var i in elementSelectors){
            var selector = elementSelectors[i];
            rects[selector] = [];
            var nodes = document.querySelectorAll(selector);
            count += nodes.length;
            nodes.forEach(function(v){
                var bb = v.getBoundingClientRect();
                rects[selector].push({
                    top:    bb.top,
                    left:   bb.left,
                    width:  bb.width,
                    height: bb.height
                });
            })
        }

        return {
            rects : rects,
            count : count
        };
    },elementSelectors);

    return ret;
};

var captureRectToFile = function(page,rect,outputFile){
    var oldRect = page.clipRect;
    page.clipRect = rect;
    page.render(outputFile);
    page.clipRect = oldRect;
    return require('fs').exists(outputFile);
};

var captureRectToBase64 = function(page,rect){
    var oldRect = page.clipRect;
    page.clipRect = rect;
    var base64Str = page.renderBase64('png');
    page.clipRect = oldRect;
    if(!base64Str){
        return false;
    }
    return 'data:image/png;base64,' + base64Str;
};

var captureElementToFile = function (page,elementSelector,outputFile) {
    return captureRectToFile(page,getElementRect(page,elementSelector),outputFile);
};

var captureElementToBase64 = function (page,elementSelector) {
    return captureRectToBase64(page,getElementRect(page,elementSelector));
};

var captureElementsToBase64Callback = function (page, elementSelectors,callBack) {
    var rets = getElementsRect(page,elementSelectors);
    for(var selector in rets.rects){
        var selectorRects = rets.rects[selector];
        for(var i = 0;i < selectorRects.length;i++){
            var imageBase64 = captureRectToBase64(page,selectorRects[i]);
            callBack(imageBase64,selector);
        }
    }
};


var loadPage = function(userOption){
    var option;
    var intervalTickId;
    var timeoutTickId;
    var page;
    var defaultOption = {
        width : 10000,
        height : 10000,
        debug : true,
        pageUrl : '',
        interval : 50,
        timeout : 5000,
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
    
    timeoutTickId = setTimeout(function () {
        option.onEnd(page);
        // 超时未渲染完成则退出
        return pageError("wait render timeout:" + option.timeout + 'ms',3)
    }, option.timeout + option.interval + 5);
    
    if(option.width && option.height){
        page.viewportSize = {width:option.width,height:option.height};
    }
    
    page.onPageCreated = function(newPage) {
        newPage.onClosing = function(closingPage) {
            console.log('A child page is closing: ' + closingPage.url);
        };
    };
    
    page.onError = function(msg, trace) {
        var msgStack = ['ERROR: ' + msg];

        if (trace && trace.length) {
            msgStack.push('TRACE:');
            trace.forEach(function(t) {
                msgStack.push(' -> ' + t.file + ': ' + t.line + (t.function ? ' (in function "' + t.function +'")' : ''));
            });
        }

        console.error(msgStack.join('\n'));
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

    // page.onPrompt = function(msg, defaultVal) {
    //     return defaultVal;
    // };
    // page.onAlert = function(msg) {
    //     console.info('[ALERT]: ' + msg);
    // };
    
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
                clearInterval(intervalTickId)
                if(timeoutTickId){
                    clearTimeout(timeoutTickId);
                }
                return pageError('failed to load the address',4);
            }

            // 加载外部JS
            // page.includeJs('https://cdn.bootcdn.net/ajax/libs/jquery/2.1.4/jquery.min.js', function() {
            //
            // });

            var checkOk = false;
            intervalTickId = setInterval(function(){
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
exports.noop = noop;
exports.toCamel = toCamel;
exports.argsParser = argsParser;
exports.captureElementToBase64 = captureElementToBase64;
exports.captureElementToFile = captureElementToFile;
exports.isArray = isArray;
exports.captureElementsToBase64Callback = captureElementsToBase64Callback;
exports.isFunction = isFunction;
exports.extend = extend;
exports.loadPage = loadPage;
exports.isPlainObject = isPlainObject;


