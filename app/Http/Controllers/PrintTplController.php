<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\PrintTpl;
use Illuminate\Support\Facades\Request;

class PrintTplController extends Controller
{
    public function list(){
        $models = PrintTpl::all();
        
        return Helper::successMsg([
            'list' => $models,
        ]);
    }
    
    public function save(){
        $attrs = Request::json("attrs");
        $tplName = $attrs['tpl_name'];
        $model = PrintTpl::firstWhere('tpl_name', $tplName);
        if(!$model){
            $model = new PrintTpl();
        }
        $model->fill($attrs); 
        if(!$model->save()){
            return Helper::failMsg("保存失败");
        }
        
        return Helper::successMsg($model);
    }
    
    public function delete(){
        $id = Request::json("id");
        $model = PrintTpl::find($id);
        if($model ) {
            $model->delete();
        }

        return Helper::successMsg();
    }
    
    public function get(){
        $tplName = Request::json('tpl_name');
        $model = PrintTpl::firstWhere('tpl_name', $tplName);

        return Helper::successMsg($model);
    }
}
