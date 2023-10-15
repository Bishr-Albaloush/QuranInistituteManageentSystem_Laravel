<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    use HasFactory;


    protected $primaryKey = 'Phone';
    protected $table = 'phones';
    protected $fillable = [
        'Number',

    ];

     ######################## Begin relations ##################

     public function users_call_phones(){
        return $this -> hasMany('App\Models\User', 'Phone_Number_Call', 'Phone');
    }

    public function users_social_phones(){
        return $this -> hasMany('App\Models\User', 'Phone_Number_Social', 'Phone');
    }

    public function mother(){
        return $this -> hasMany('App\Models\Mother', 'Phones_Phone', 'Phone');
    }

    public function father(){
        return $this -> hasMany('App\Models\Fahter', 'Phones_Phone', 'Phone');
    }

    public function kin(){
        return $this -> hasMany('App\Models\Kin', 'Phones_Phone', 'Phone');
    }

    ######################## end relations ##################
}
