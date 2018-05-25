<?php

namespace TinkLabs\Bank\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 大转盘活动模型类
 */
class Account extends Model
{	
           
    protected $table = 'accounts';
    
    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    protected $hidden = [];
    
    
}