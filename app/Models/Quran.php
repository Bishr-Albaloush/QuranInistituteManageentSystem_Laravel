<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quran extends Model
{
    use HasFactory;

    protected $primaryKey = 'ID_Quran';
    protected $table = 'qurans';
    protected $fillable = [
        'ID_Quran',
        'Note',
        'Narrations_ID_Narration',

    ];

    ######################## Begin relations ##################

    public function narration(){
        return $this -> belongsTo('App\Models\Narration', 'Narration_ID_Narration');
    }

    public function sections(){
    return $this -> hasMany('App\Models\Section','Qurans_ID_Quran','ID_Quran');
    }

    ######################## end relations ##################

}
