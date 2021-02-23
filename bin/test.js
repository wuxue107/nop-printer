var system = require('system');
var helper = require('./phantom/helper.js');

var command = helper.argsParser(system.args.slice(1));

console.log(command.getOption("name","default"));
console.log(command.getOption("sleep-time",100));
console.log(command.getOption("isDefault",true));
console.log(command.getOption("noExist"));

