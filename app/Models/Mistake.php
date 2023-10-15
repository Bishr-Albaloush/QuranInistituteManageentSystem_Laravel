<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mistake extends Model
{
    use HasFactory;
    protected $primaryKey = 'ID_Mistake';
    protected $table = 'mistakes';
    protected $fillable = [
        'Mistake_Type',
        'Point_Count',
        'Events_ID_Event '
    ];

      ######################## Begin relations ##################

      public function event(){
        return $this -> belongsTo('App\Models\Event', 'Events_ID_Event');
    }

    public function reciting(){
        return $this -> hasOne('App\Models\Reciting', 'Mistakes_ID_Mistake', 'ID_Mistake');
    }

    public function test(){
        return $this -> hasOne('App\Models\Test', 'Mistake', 'ID_Mistake');
    }

    ######################## end relations ##################
}
