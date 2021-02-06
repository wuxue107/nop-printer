Helper ={
    resolveValue : function(v,param,target){
        if(target === undefined){
            target = null;
        }
        param = param || [];
        if($.isFunction(v)){
            v = v.apply(target,param);
        }

        return $.when(v);
    },
    /**
     * 队列依次执行
     *
     * @param name
     * @param endFun
     * @returns {pool}
     */
    queuePool : function(name,endFun){
        name = name || Helper.generateId();
        endFun = endFun || $.noop;

        var delayMicroSecond = 0;
        var isListen = false;
        var listenTickId = 0;
        var jobs = [];
        var pool = function () {
        };

        var endPool = function(){
            try{
                endFun();
            }catch (e) {
                console.log(e);
            }

            if(isListen && listenTickId === 0 ){
                setTimeout(function () {
                    pool.listen();
                },50);
            }
        };

        var makeJob = function(workFun){
            return function () {
                if(delayMicroSecond > 0){
                    setTimeout(function () {
                        workFun( jobs.shift() || endPool)
                    },delayMicroSecond)
                }else{
                    workFun( jobs.shift() || endPool);
                }
            }
        };

        pool.getName = function () {
            return name;
        };

        /**
         * 每个job之间的延时
         *
         * @param delay 毫秒
         * @returns {pool}
         */
        pool.delay = function(delay){
            delayMicroSecond = !!delay;

            return pool;
        };

        /**
         *
         * @param  workFunc function(nextFunc)
         * @param first bool
         *
         * @returns {pool}
         */
        pool.queue =  function (workFunc,first) {
            if(first){
                jobs.push(makeJob(workFunc));
            }else{
                jobs.unshift(makeJob(workFunc));
            }

            return pool;
        };

        /**
         * 将数据应用于workFunc依次队列执行
         *
         * @param items 数据列表
         * @param workFunc function(nextFunc,item,itemIndex)
         * @returns {pool}
         */
        pool.queueWithDataItems = function(items,workFunc){
            for (let index in items){
                jobs.push(makeJob(function(next){
                    workFunc(next,items[index],index)
                }));
            }
            return pool;
        };

        /**
         * 手动出队开始执行任务
         */
        pool.dequeue =  function () {
            var job = jobs.shift();
            if(job){
                job();
            }

            return false;
        };

        /**
         * 清空队列
         *
         * @returns {pool}
         */
        pool.clear = function () {
            jobs = [];
            return pool;
        };

        /**
         * 监听队列，只要有任务就执行
         *
         * @returns {pool}
         */
        pool.listen = function () {
            isListen = true;
            if(listenTickId === 0) {
                listenTickId = setInterval(function () {
                    if(jobs.length > 0){
                        clearInterval(listenTickId);
                        listenTickId = 0;
                        pool.dequeue();
                    }
                },50);
            }

            return pool;
        };

        /**
         * 取消队列监听
         *
         * @returns {pool}
         */
        pool.cancelListen = function () {
            isListen = false;
            if(listenTickId){
                clearInterval(listenTickId);
            }
            listenTickId = 0;
            return pool;
        };

        return pool;
    },

    generateId : function(prefix){
        var thisFunc = arguments.callee;
        if(thisFunc._id === undefined){
            thisFunc._id = (new Date()).getTime();
        }
        thisFunc._id ++;
        prefix = prefix || 'id-';
        return prefix + thisFunc._id;
    },

    postJson : function(url,data,callback,dataType){
        dataType = dataType || 'json';

        return $.ajax({
            url: url,
            type: 'POST',
            dataType: dataType,
            data: JSON.stringify(data),
            headers : {
                "Content-Type" : "application/json"
            },
            success: callback
        })
    },
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
    deleteRow: function (el,callback) {
        el = $(el);
        var tableInfo = Helper.getTableInfo(el);
        if(!tableInfo.currentRowId){
            return false;
        }

        var layerIndex = layer.confirm("确认要删除此条记录吗？", function () {
            var deleteFun = function () {
                layer.close(layerIndex);
                tableInfo.table.bootstrapTable('remove', {
                    field: tableInfo.idField,
                    values: [tableInfo.currentRowId]
                });
            };

            callback(deleteFun,tableInfo.currentRowId,tableInfo.currentRowData)
        });

        return false;
    },
    
    dataPath : function(path,source,defaultValue){
        if(typeof source === 'undefined' ){
            source = window;
        }
        if(typeof defaultValue === 'undefined'){
            defaultValue = null
        }
        try{
            var ret = eval('source' + ((path !== undefined && path !== '' && path != null) ?('.' + path):''));
            if(ret === null || ret === '' || ret === undefined){
                return defaultValue;
            }

            return ret;
        }catch (e) {
            return defaultValue;
        }
    },

}
