<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>打印机设置</title>
<meta charset="utf-8"/>
<link rel="shortcut icon" href="favicon.ico">
<link href="/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
<script src="/js/jquery.min.js?v=2.1.4"></script>
<script src="/js/lodash.min.js?v=4.17.20"></script>
<script src="/js/bootstrap.min.js?v=3.3.6"></script>
<script src="/js/helper.js?v=3.3.6"></script>
<link href="/css/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
<script src="/js/bootstrap-table/bootstrap-table.min.js"></script>
<script src="/js/bootstrap-table/locale/bootstrap-table-zh-CN.min.js"></script>
<script src="/js/layer/layer.js"></script>
</head>
<body>

<script>
   localPrinters = {!! json_encode($localPrinters) !!};
</script>
<div class="contenter">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="bs-bars pull-left">
            <div id="toolbar">

                <div class="btn-group" id="exampleTableEventsToolbar" role="group">
                    <a class="btn btn-outline btn-default new-tab">
                        添加打印机：
                    </a>
                    <a class="btn btn-default" style="width: 200px">
                        <select id="localPrinterInput" class="" name="type" style="border: none;width: 100%">
                            <option value=""> - 选择打印机 - </option>
                        </select>
                    </a>
                    <a class="btn btn-outline btn-default new-tab" id="add-printer" data-title="添加">
                        <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
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
        <table id="main_table" data-toggle="table" data-url="/api/printer/get-config"
               data-method="get"
               data-mobile-responsive="true"
               data-response-handler="bootstrapTableResponseHandler"
               data-id-field="Name"
               data-query-params="queryParams"
               data-toolbar="#toolbar"
               data-striped="true"
               data-show-refresh="true"
               data-search="false"
               data-pagination="false" data-side-pagination="server" data-page-size="30">
            <thead>
            <tr>
                <th data-field="Name" data-width="200px" data-formatter="<span>%s</span>">打印机名称</th>
                <th data-field="isDefault" data-width="80px" data-formatter="<span>%s</span>">是否默认</th>

                <th data-width="100" data-formatter="columnFormatter.operation">操作</th>
            </tr>
            </thead>
        </table>
    </div>
</div>
<script type="text/javascript">
    function bootstrapTableResponseHandler(res) {
        if (res.code != 0) {
            return {"total": 0, "rows": []}
        }

        var printers = [];
        if(res.data.printers){
            for(var name in res.data.printers){
                var printer = res.data.printers[name];
                printer.isDefault = name === res.data.default?'是':'否';
                printers.push(printer)
            }
        }
        return {
            "total": printers.length,
            "rows": printers
        }
    }
    function queryParams(params) {
        return params;
    }

    function deletePrinter(el){
        Helper.deleteRow(el,function(deleteFunc,id,rowData){
            Helper.postJson('/api/printer/remove-printer-config',{printer_name: id}).then(function(d){
                layer.msg(d.msg);
                $table.bootstrapTable('refresh');
            })
        })
    }

    function defaultPrinter(el){
        var id = Helper.getRowId(el);
        Helper.postJson('/api/printer/set-printer-config',{printer_name: id,is_default : 1}).then(function(d){
            layer.msg(d.msg);
            $table.bootstrapTable('refresh');
        })
    }

    columnFormatter = {
        operation: function (v, row, index) {
             return '<button onclick="deletePrinter(this);return false;" class="btn btn-sm btn-danger" tabindex="-1">删除</button>'
                + (row.isDefault?'':'<button onclick="defaultPrinter(this);return false;" style="margin: 0 10px" class="btn btn-sm btn-success" tabindex="-1">设为默认</button>');
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

    var localPrinterEl = $('#localPrinterInput');
    $('#add-printer').click(function(){
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

    $(localPrinters).each(function(){
        return $('<option value="'+this.Name+'">'+this.Name+'</option>').data(this).appendTo(localPrinterEl);
    });


    $(function () {
        $('.filter-query').change(function () {
            $table.bootstrapTable('refresh');
        });

        // 选择批量删除
        $('#batch_remove').click(function () {
            Helper.deleteSelections(this, Helper.routeUrl('sys-config/api-batch-delete'));
        });
    })
</script>
</body>
</html>

