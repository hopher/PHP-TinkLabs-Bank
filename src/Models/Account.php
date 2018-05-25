<?php

namespace TinkLabs\Bank\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 帐户模型类
 */
class Account extends Model
{	
           
    protected $table = 'accounts';
    
    protected $primaryKey = 'uid';

    protected $guarded = [];

    protected $hidden = [];
    
}