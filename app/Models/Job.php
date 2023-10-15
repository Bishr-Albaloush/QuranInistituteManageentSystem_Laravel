<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $primaryKey = 'ID_Job';
    protected $table = 'jobs';
    protected $fillable = [
        'Job_Name',

    ];

    ######################## Begin relations ##################

    public function user(){
        return $this -> hasMany('App\Models\User', 'Job_ID_Job', 'ID_Job');
    }

    public function mother(){
        return $this -> hasMany('App\Models\Mother', 'Job_ID_Job', 'ID_Job');
    }

    public function father(){
        return $this -> hasMany('App\Models\Fahter', 'Job_ID_Job', 'ID_Job');
    }

    public function kin(){
        return $this -> hasMany('App\Models\Kin', 'Job_ID_Job', 'ID_Job');
    }

    ######################## end relations ##################

}
