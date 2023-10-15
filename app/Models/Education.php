<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory;

    protected $primaryKey = 'ID_Education';
    protected $table = 'educations';
    protected $fillable = [
        'Education_Type',
        'Major_ID_Major',
    ];

      ######################## Begin relations ##################

      public function major(){
        return $this -> belongsTo('App\Models\Major', 'Major_ID_Major');
    }

    public function user(){
        return $this -> hasMany('App\Models\User', 'Edducation_ID_Edducation', 'ID_Education');
    }

    ######################## end relations ##################
}
