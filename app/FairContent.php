<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FairContent extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table       = 'fair_content';
    protected $guarded = ['id', 'delete_flg', 'create_date', 'update_date'];
    const CREATED_AT = 'create_date';
    const UPDATED_AT = 'update_date';

    public function fair()
    {
        return $this->belongsTo('App\Fair');
    }
}
