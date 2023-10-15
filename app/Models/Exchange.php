<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
{
    use HasFactory;

    protected $primaryKey = 'ID_Exchange';
    protected $table = 'exchanges';
    protected $fillable = [
        'Season',
        'Price',
        'Value',
    ];

      ######################## Begin relations ##################

    public function season(){
        return $this -> belongsTo('App\Models\Season', 'Season');
    }

    public function points(){
        return $this -> hasMany('App\Models\Point', 'Exchanges_ID_Exchange', 'ID_Exchange');
    }

    ######################## end relations ##################

}
