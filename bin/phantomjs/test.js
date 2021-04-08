var system = require('system');
var helper = require('./lib/helper.js');

var command = helper.argsParser(system.args.slice(1));

console.log(command.getOption("name","default"));
console.log(command.getOption("sleep-time",100));
console.log(command.getOption("help",false));
console.log(command.getOption("noExist"));

var option = helper.extend({},{name:111,obj:{"some":111}},{obj:{"tt":222}});
console.log(option);

var option = helper.extend(true,{name:111,obj:{"some":111}},{obj:{"tt":222}});
console.log(JSON.stringify(command));
phantom.exit();
