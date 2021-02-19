<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintTpl extends Model
{
    use HasFactory;

    protected $table = 'print_tpl';
    
    protected $fillable = [
        'tpl_name',
        'tpl_content',
        'params_examples',
    ];
    
}
