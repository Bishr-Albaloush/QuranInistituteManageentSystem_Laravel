<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    use HasFactory;

    protected $primaryKey = 'ID_Point';
    protected $table = 'points';
    protected $fillable = [
        'Exchanges_ID_Exchange',
    ];

    ######################## Begin relations ##################

    public function exchange(){
        return $this -> belongsTo('App\Models\Exchange', 'Exchanges_ID_Exchange');
    }

    ######################## end relations ##################

}
