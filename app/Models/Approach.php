<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approach extends Model
{
    use HasFactory;

    protected $primaryKey = 'ID_Approach';
    protected $table = 'approachs';
    protected $fillable = [
        'ID_Approach',
        'Approach_Name',
    ];

    ######################## Begin relations ##################

    public function narration(){
        return $this -> hasOne('App\Models\Narration', 'Approachs_ID_Approach', 'ID_Approach');
    }

    ######################## end relations ##################
}
