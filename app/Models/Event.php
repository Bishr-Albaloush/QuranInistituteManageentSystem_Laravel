<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $primaryKey = 'ID_Event';
    protected $table = 'events';
    protected $fillable = [
        'Event_Name',
        'Mistake_Point_Percent',
        'Rate_Point_Percent',
        'Season',
        'State'
    ];

      ######################## Begin relations ##################

    public function state(){
        return $this -> belongsTo('App\Models\State', 'State');
    }

    public function season(){
        return $this -> belongsTo('App\Models\Season', 'Season');
    }

    public function mistake(){
        return $this -> hasMany('App\Models\Mistake', 'Events_ID_Event', 'ID_Event');
    }

    public function rate(){
        return $this -> hasMany('App\Models\Rate', 'Events_ID_Event', 'ID_Event');
    }

    ######################## end relations ##################

}
