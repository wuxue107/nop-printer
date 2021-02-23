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
    };

    var index = 1;
    for(var i in optionArgs){
        var optionArg = optionArgs[i];
        var matchs = optionArg.match(/^--([\w-]+)(=(.*))?$/);
        if(!matchs){
            options[index] = optionArg;
            index++;
        }else{
            var optionName = toCamel(matchs[1]);
            options[optionName] = matchs[3]
        }
    }

    options.getOption = function(name,defaultValue){
        var name = toCamel(name);
        if(options[name] === undefined){
            return defaultValue;
        }
        
        return options[name];
    };

    return options;
};


exports.trim = trim;
exports.toCamel = toCamel;
exports.argsParser = argsParser;
