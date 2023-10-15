<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    use HasFactory;

    protected $primaryKey = 'ID_Season';
    protected $table = 'seasons';
    protected $fillable = [
        'Season_Name',
        'Years_ID_Year',
    ];

    ######################## Begin relations ##################

    public function year(){
        return $this -> belongsTo('App\Models\Year', 'Years_ID_Year');
    }

    ######################## end relations ##################

}
