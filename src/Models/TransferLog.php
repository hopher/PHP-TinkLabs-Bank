<?php

namespace TinkLabs\Bank\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 转帐日志模型类
 */
class TransferLog extends Model
{	
           
    protected $table = 'account_transfer_logs';
    
    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $hidden = [];    
    
}