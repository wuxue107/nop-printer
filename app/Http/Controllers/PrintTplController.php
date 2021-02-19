<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\PrintTpl;
use Illuminate\Support\Facades\Request;

class PrintTplController extends Controller
{
    public function create(){
        $attrs = Request::json("attrs");
        $model = new PrintTpl($attrs);
        if(!$model->save()){
            return Helper::failMsg("添加失败");
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
    
    
}
