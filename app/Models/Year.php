<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
    use HasFactory;

    protected $primaryKey = 'ID_Year';
    protected $table = 'years';
    protected $fillable = [
        'Year_Number',
    ];

    ######################## Begin relations ##################

    public function season(){
        return $this -> hasMany('App\Models\Season', 'Years_ID_Year', 'ID_Year');
    }

    ######################## end relations ##################

}
