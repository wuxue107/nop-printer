var Routes = require('./lib/route.js'),
    helper = require('./lib/helper.js'),
    app = new Routes();

var command = helper.argsParser();
var listenPort = command.getArgs('port',8078),
    isHelper = command.getOption('help',false);

if(isHelper){
    var helpContent =
        "    Usage : \n" +
        "    phantomjs capture.js [--help] [--port=8078]\n" +
        "        --help                show help information\n" +
        "        --port=8078           listen port of web server\n" +
        "" +
        "";

    console.log(helpContent);
    phantom.exit();
}

app.use(function(req,res,next){
    // if(req.post.width && req.post.height){
    //     if(isNaN(parseInt(req.post.width)) && isNaN(parseInt(req.post.height))){
    //         req.post.width = Math.abs(Math.floor(req.post.width));
    //         req.post.height = Math.abs(Math.floor(req.post.height));
    //     }
    //     else{
    //         req.post.width = null;
    //         req.post.height = null;
    //     }
    // }
    next();
});

app.post('/',function(request, response) {
    var postParam = JSON.parse(request.post);
    var element = postParam.element || 'body';
    var res = {code : 0,msg : 'success',data : null};

    var timeout = ~~postParam.timeout;
    var option = {
        pageUrl : postParam.pageUrl,
        timeout : timeout || 9000,
        debug : true,
        onSuccess : function(page){
            res.data = {
                image_data : helper.captureElementToBase64(page,element)
            };
        },
        onError : function(page,errorMsg,errorCode){
            res.code = errorCode;
            res.msg = errorMsg;
        },
        onEnd : function (page) {
            response.send(res);
        }
    };
    
    if(postParam.width){
        option.width = ~~postParam.width;
    }
    
    if(postParam.height){
        option.height = ~~postParam.height;
    }
    
    helper.loadPage(option);
});

app.listen(listenPort);

console.log('Listening on port ' + listenPort);
