<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Father extends Model
{
    use HasFactory;

    protected $primaryKey = 'ID_Father';
    protected $fillable = [
        'Father_Name',
        'State_ID_State',
        'Job_ID_Job',
        'Phones_Phone'
    ];

     ######################## Begin relations ##################

     public function state(){
        return $this -> belongsTo('App\Models\State', 'State_ID_State');
    }

    public function job(){
        return $this -> belongsTo('App\Models\Job', 'Job_ID_Job');
    }

    public function user(){
        return $this -> hasMany('App\Models\User', 'Fathers_ID_Father', 'ID_Father');
    }

    public function phone(){
        return $this -> belongsTo('App\Models\Phone', 'Phones_Phone');
    }

    ######################## end relations ##################
}
