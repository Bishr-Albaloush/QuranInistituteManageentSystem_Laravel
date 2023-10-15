<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    use HasFactory;

    
    protected $primaryKey = 'ID_Rate';
    protected $table = 'rates';
    protected $fillable = [
        'Rate_Name',
        'Point_Count',
        'Events_ID_Event',
    ];

      ######################## Begin relations ##################

    public function reciting(){
        return $this -> hasMany('App\Models\Reciting', 'Rates_ID_Rate', 'ID_Rate');
    }

    public function test(){
        return $this -> hasMany('App\Models\Test', 'Rate', 'ID_Rate');
    }

    public function event(){
        return $this -> belongsTo('App\Models\Mistake', 'Events_ID_Event');
    }

    ######################## end relations ##################
}
