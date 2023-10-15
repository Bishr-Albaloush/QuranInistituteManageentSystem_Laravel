<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Narration extends Model
{
    use HasFactory;

    protected $primaryKey = 'ID_Narration';
    protected $table = 'narrations';
    protected $fillable = [
        'ID_Narration',
        'Narration_Name',
        'Narrated_From_Name',
        'Approachs_ID_Approach'
    ];

    ######################## Begin relations ##################

    public function approach(){
        return $this -> belongsTo('App\Models\Approach', 'Approachs_ID_Approach');
    }

    public function quran(){
        return $this -> hasOne('App\Models\Quran', 'Narrations_ID_Narration', 'ID_Narration');
    }

    ######################## end relations ##################
}
