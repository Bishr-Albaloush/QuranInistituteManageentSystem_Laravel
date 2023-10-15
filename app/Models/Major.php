<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    use HasFactory;

    protected $primaryKey = 'ID_Major';
    protected $fillable = [
        'Major_Name',
        'Year',
    ];

    ######################## Begin relations ##################

    public function education(){
        return $this -> hasOne('App\Models\Education', 'Major_ID_Major', 'ID_Major');
    }

    ######################## end relations ##################
}
