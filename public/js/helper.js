Helper ={
    operation: function (items,isStyleBtn) {
        var text = '<div class="operation btn-group pull-right '+ (isStyleBtn?'style-btn':'') +' ">' +
            '<button id="w2" class="btn btn-default btn-xs dropdown-toggle" href="#" data-toggle="dropdown" aria-expanded="true">' +
            '操作 <span class="caret"></span>' +
            '</button>' +
            '<ul id="w3" class="dropdown-menu">';
        for (var index in items) {
            text += '<li>' + items[index] + '</li>';
        }
        text += '</ul>' +
            '</div>';
        return text;
    },

    /**
     * bootstrap table 辅助方法
     */

    /** 根据子节点获取table节点  **/
    getTable: function (subNode) {
        var el = $(subNode);
        return el.closest('.fixed-table-body').find('table');
    },
    /** 根据子节点获取所在table的信息 **/
    getTableInfo: function (el) {
        el = $(el);
        var currentRowIndex = el.closest('tr').data('index');
        var table = el.closest('.bootstrap-table').find('.fixed-table-body>table');
        var idField = table.data('bootstrap.table').options.idField;
        idField = idField ? idField : 'id';
        var tableData = table.bootstrapTable('getData', true);
        var currentRowData = currentRowIndex !== undefined?tableData[currentRowIndex]:null;
        var currentRowId = currentRowData?currentRowData[idField]:null;

        var selectionData = table.bootstrapTable('getSelections');
        var selectionId = $.map(selectionData, function (row) {
            return row[idField];
        });
        return {
            currentRowId : currentRowId,
            currentRowData : currentRowData,
            selectionId: selectionId,
            selectionData: selectionData,
            idField: idField,
            table: table,
            tableData: tableData
        };
    },

    /** 根据子节点获取表格元数据 **/
    getTableData: function (subNode) {
        var table = Helper.getTable(subNode);
        return table.bootstrapTable('getData', true);
    },

    /** 根据子节点获取该行元数据 **/
    getRowData: function (subNode) {
        var el = $(subNode);
        var rowIndex = $(subNode).closest('tr').data('index');
        var tableData = Helper.getTableData(el);
        return tableData[rowIndex];
    },

    /** 根据子节点获取该行元数据的id字段值 **/
    getRowId: function (subNode, idField) {
        var rowData = Helper.getRowData(subNode);
        idField = typeof idField == 'undefined' ? Helper.getTable(subNode).data('bootstrap.table').options.idField : idField;
        return Helper.dataPath(idField,rowData);
    },


    /** 获取table指定字段列的所有单元格 **/
    getCellsByColumn: function (table, field, filterFunc) {
        var cells = table.find('>tbody>tr>td:nth-child(' + (table.find('>thead>tr>th[data-field="' + field + '"]').index() + 1) + ')');
        if(!$.isFunction(filterFunc)){
            return cells;
        }

        var tableData = table.bootstrapTable('getData', true);
        return cells.filter(function(rowIndex){
            var row = tableData[rowIndex];
            try{
                return filterFunc(row);
            }catch (e) {
                console.error(e);
                return false;
            }
        });
    },

    /** 删除子节点，该行记录 **/
    deleteRow: function (el,url) {
        el = $(el);
        if(!url) url = el.attr('href');
        if(!url) url = el.data('url');

        var tableInfo = Helper.getTableInfo(el);
        if(!tableInfo.currentRowId){
            return false;
        }

        var layerIndex = layer.confirm("确认要删除此条记录吗？", function () {
            var data = {};
            Helper.postApi(url, data,function (d) {
                layer.close(layerIndex);
                layer.msg(d.msg);
                if(d.code == 0){
                    tableInfo.table.bootstrapTable('remove', {
                        field: tableInfo.idField,
                        values: [tableInfo.currentRowId]
                    });
                }
            },"json");
        });

        return false;
    },

}
