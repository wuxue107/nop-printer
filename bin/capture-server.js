var system = require('system'),
    Routes = require('./phantom/route.js'),
    helper = require('./phantom/helper.js'),
    app = new Routes();

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
    var element = request.post.element || 'body';
    var res = {code : 0,msg : 'success',data : null};
    var postParam = JSON.parse(request.post);
    var option = {
        pageUrl : postParam.pageUrl,
        timeout : 9000,
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

app.listen(system.args[1] || 8078);

console.log('Listening on port ' + (system.args[1] || 8078));
