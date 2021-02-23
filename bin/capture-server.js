var system = require('system'),
    page = require('webpage').create(),
    Routes = require('./phantom/route.js'),
    helper = require('./phantom/helper.js'),
    app = new Routes();

page.viewportSize = {width: 1366, height: 768};

app.use(function(req,res,next){
    if(req.post.width && req.post.height){
        if(isNaN(parseInt(req.post.width)) && isNaN(parseInt(req.post.height))){
            req.post.width = Math.abs(Math.floor(req.post.width));
            req.post.height = Math.abs(Math.floor(req.post.height));
        }
        else{
            req.post.width = null;
            req.post.height = null;
        }
    }
    next();
});

app.post('/',function(request, response) {
    var element = request.post.element || 'body';
    var res = {code : 0,msg : 'success',data : null}
    var option = {
        pageUrl : request.post.pageUrl,
        timeout : 9000,
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
            response.send(JSON.stringify(response))
        }
    };
    
    if(request.post.width){
        option.width = ~~req.post.width;
    }
    if(request.post.height){
        option.height = ~~req.post.height;
    }
    
    helper.loadPage(option);
});

app.listen(system.args[1] || 8000);

console.log('Listening on port ' + (system.args[1] || 8088));
