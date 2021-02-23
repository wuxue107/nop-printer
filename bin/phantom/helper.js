// This is to be used by "module.js" (and "module.coffee") example(s).
// There should NOT be a "universe.coffee" as only 1 of the 2 would
//  ever be loaded unless the file extension was specified.

"use strict";

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
        if(ret === undefined){
            return defaultValue;
        }
        if(defaultValue === true || defaultValue === false){
            return !!ret;
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


exports.trim = trim;
exports.toCamel = toCamel;
exports.argsParser = argsParser;
