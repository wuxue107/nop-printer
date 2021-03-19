<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>打印模板管理</title>
<meta charset="utf-8"/>
<link rel="shortcut icon" href="favicon.ico">
<link href="/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
@include('tpl-common')
<script src="/js/bootstrap.min.js?v=3.3.6"></script>
<script src="/js/helper.js?v=3.3.6"></script>
<link href="/css/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
<script src="/js/bootstrap-table/bootstrap-table.min.js"></script>
<script src="/js/bootstrap-table/locale/bootstrap-table-zh-CN.min.js"></script>
<script src="/js/layer/layer.js"></script>
</head>
<body>

<div id="tpl-edit-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">编辑打印模板</h4>
            </div>
            <div class="modal-body">
                <form id="tpl-form" class="form-horizontal m-t" action="/index.php?r=gii-test%2Fcreate" method="post">
                    <div class="form-group field-giitest-user_id required">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><label class="control-label">模板名称</label>：</label>
                            <div class="col-sm-8">
                                <input type="text" id="input_tpl_name" class="form-control" name="attrs[tpl_name]" aria-required="true">
                            </div>
                        </div>
                    </div>
                    <div class="form-group field-giitest-user_id required">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><label class="control-label">宽度</label>：</label>
                            <div class="col-sm-8">
                                <input type="number" id="input_width" class="form-control" min="0" name="attrs[width]" aria-required="true">
                            </div>
                        </div>
                    </div>
                    <div class="form-group field-giitest-user_id required">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><label class="control-label">高度</label>：</label>
                            <div class="col-sm-8">
                                <input type="number" id="input_height" class="form-control" min="0" name="attrs[height]" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="form-group field-giitest-user_id required">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><label class="control-label">边距</label>：</label>
                            <div class="col-sm-8">
                                <input type="string" id="input_padding" class="form-control" min="0" name="attrs[padding]" value="40px 0px 40px 0px">
                            </div>
                        </div>
                    </div>
                    <div class="form-group field-giitest-user_id required">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><label class="control-label">模板内容</label>：</label>
                            <div class="col-sm-8">
                                <textarea rows=6 id="input_tpl_content" class="form-control" name="attrs[tpl_content]" aria-required="true"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-group field-giitest-user_id required">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><label class="control-label">示例参数</label>：</label>
                            <div class="col-sm-8">
                                <textarea rows="6" id="input_params_examples" class="form-control" name="attrs[params_examples]" aria-required="true">{}</textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="save-btn" class="btn btn-success ">保存</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="contenter">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="bs-bars pull-left">
            <div id="toolbar">
                <div class="btn-group" id="exampleTableEventsToolbar" role="group">
                    <a class="btn btn-outline btn-default new-tab" id="add-tpl" style="background-color: #5cb85c; color: white;" data-title="添加">
                        <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>添加模板
                    </a>
                </div>
            </div>
        </div>
        <div id="extend_filter" class="fixed-table-toolbar">
        </div>
        <script>

            $(function () {
                $table.closest('.bootstrap-table').find('.fixed-table-toolbar>.search,.fixed-table-toolbar>.btn-group').last().after($('#extend_filter'));
            })
        </script>
        <table id="main_table" data-toggle="table" data-url="/api/print-tpl/list"
               data-method="get"
               data-mobile-responsive="true"
               data-response-handler="bootstrapTableResponseHandler"
               data-id-field="id"
               data-query-params="queryParams"
               data-toolbar="#toolbar"
               data-striped="true"
               data-show-refresh="true"
               data-search="false"
               data-pagination="false" data-side-pagination="server" data-page-size="30">
            <thead>
            <tr>
                <th data-field="id" data-width="50" data-formatter="<span>%s</span>">ID</th>
                <th data-field="tpl_name" data-width="150" data-formatter="<span>%s</span>">名称</th>
                <th data-field="width" data-width="50" data-formatter="<span>%s</span>">宽度</th>
                <th data-field="height" data-width="50" data-formatter="<span>%s</span>">高度</th>
                <th data-field="tpl_content" data-formatter="columnFormatter.tpl_content">模板</th>
                <th data-field="params_examples" data-formatter="columnFormatter.params_examples">示例参数</th>

                <th data-width="150" data-formatter="columnFormatter.operation">操作</th>
            </tr>
            </thead>
        </table>
    </div>
</div>
<script type="text/javascript">
    var loadingId;
    $(document).ajaxSend(function () {
        loadingId = layer.load(1, {
            shade: [0.1,'#fff'] //0.1透明度的白色背景
        });
    }).ajaxStop(function(){
        layer.close(loadingId);
    }).ajaxComplete(function(){
        layer.close(loadingId);
    });

    function bootstrapTableResponseHandler(res) {
        if (res.code != 0) {
            return {"total": 0, "rows": []}
        }
        
        return {
            "total": res.data.list.length,
            "rows": res.data.list
        }
    }
    
    function queryParams(params) {
        return params;
    }

    function deleteTpl(el){
        Helper.deleteRow(el,function(deleteFunc,id,rowData){
            Helper.postJson('/api/print-tpl/delete',{tpl_name: rowData.tpl_name}).then(function(d){
                layer.msg(d.msg);
                $table.bootstrapTable('refresh');
            })
        })
    }

    function showTplEdit(data){
        console.log("init data");
        console.log(data);
        var modalEl = $('#tpl-edit-modal');
        modalEl.modal('show')
        
        var defaultAttrs = {
            'tpl_name' : '',
            'tpl_content':'',
            'padding':'40px 0px 40px 0px',
            'width':0,
            'height' : 0,
            'params_examples' : '{}'
        };
        for(var attr in defaultAttrs){
            var val = data[attr];
            if(val === undefined || val === null){
                val = defaultAttrs[attr];
            }
            $('#input_' + attr).val(val);
        }
    } 
    
    $('#add-tpl').click(function(){
        showTplEdit({});
    });
    
    $('#save-btn').click(function (){
        var defaultAttrs = {
            'tpl_name' : '',
            'tpl_content':'',
            'padding':'40px 0px 40px 0px',
            'width':0,
            'height' : 0,
            'params_examples' : '{}'
        };
    
        var attrs = {};
        for(var attr in defaultAttrs){
            attrs[attr] = $('#input_' + attr).val();
            if(attrs[attr] === '') {
                attrs[attr] = defaultAttrs[attr];
            }
        }
        var params;
        try{
            params = JSON.parse(attrs['params_examples']);
            if(!$.isPlainObject(params)){
                layer.msg("无效的示例模板参数");
                return;
            }
        }catch (e) {
            layer.msg("无效的示例模板参数,无效JSON");
            return;
        }
        
        Helper.postJson('/api/print-tpl/save',{attrs: attrs}).then(function(d){
            layer.msg(d.msg);
            $table.bootstrapTable('refresh');
            if(d.code === 0){
                $('#tpl-edit-modal').modal('hide')
            }
        });
    });
    function editTpl(el){
        var row = Helper.getRowData(el);
        showTplEdit(row);
    }

    function testTpl(el){
        var row = Helper.getRowData(el);
        Helper.postJson('/api/job/print-tpl',{tpl_name: row.tpl_name,tpl_params: JSON.parse(row.params_examples)}).then(function(d){
            layer.msg(d.msg);
        });
    }
    
    var demoZoom = 0.7;
    columnFormatter = {
        operation: function (v, row, index) {
             return '<button onclick="deleteTpl(this);return false;"  style="margin: 5px" class="btn btn-sm btn-danger" tabindex="-1">删除</button>'
                + (row.isDefault === '是'?'':'<button onclick="editTpl(this);return false;" style="margin: 5px" class="btn btn-sm btn-success" tabindex="-1">编辑</button>')
                + '<button onclick="testTpl(this);return false;" style="margin: 5px" class="btn btn-sm btn-success" tabindex="-1">模板打印测试</button>';
        },
        params_examples : function(v,row,index){
            try {
                v = JSON.stringify(JSON.parse(v),null,2)
            }catch(e){
                v = e.toString();
            }
            return '<pre>' + v + '</pre>'
        },
        tpl_content : function (v, row, index) {
            var params;
            var errorMsg = null;
            try{
                params = JSON.parse(row.params_examples);
            }catch (e) {
                errorMsg = "模板示例参数错误：" + e.toString();
            }
            
            return  '<div class="pages" style="width:'+(row.width * demoZoom)+'px"><div style="width: '+(row.width)+'px;margin: 0 auto; position: relative;border: 1px solid #666;background: white;transform: scale('+demoZoom+','+demoZoom+');transform-origin: 0 0;">' + renderTpl(errorMsg,true,row,params,'') + '</div></div>';
        }
    };

    $table = $('#main_table');

    function tableHeight() {
        var h = $(window).height() - $table.outerHeight(true);
        return h < 300 ? 300 : h;
    }

    $table.bootstrapTable(
        {
            height: tableHeight()
        });
    
    $('#add-tpl').click(function(){
        var selectValue = localPrinterEl.val();
        if(selectValue === ""){
            return;
        }

        Helper.postJson('/api/printer/set-printer-config',{
            printer_name : selectValue
        }).then(function(d){
            layer.msg(d.msg);
            $table.bootstrapTable('refresh');
        })
    });

    $(function () {
        $('.filter-query').change(function () {
            $table.bootstrapTable('refresh');
        });

        $table.on('load-success.bs.table',function(){
            processTpl();

            $('.page').each(function(){
                var el = $(this);
                var w = el.parent().parent();
                w.css({height:el.height() * demoZoom + 50,width:el.width() * demoZoom});
            })
            setTimeout(function () {$table.resize();},1500);
        });

    })
</script>
</body>
</html>

