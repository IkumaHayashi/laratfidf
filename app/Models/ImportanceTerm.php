<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportanceTerm extends Model
{
    public function text(){
        return $this->belongsTo('App\Models\Text');
    }

}
