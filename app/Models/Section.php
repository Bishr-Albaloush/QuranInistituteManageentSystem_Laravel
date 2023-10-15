<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $primaryKey = 'ID_Section';
    protected $table = 'sections';
    protected $fillable = [
        'ID_Section',
        'Section_Number',
        'Qurans_ID_Quran',
    ];

    ######################## Begin relations ##################

    public function quran(){
        return $this -> belongsTo('App\Models\Quran', 'Qurans_ID_Quran');
    }

    public function pages(){
        return $this -> hasMany('App\Models\Page', 'Sections_ID_Section', 'ID_Section');
    }

    public function test(){
        return $this -> hasMany('App\Models\Test', 'Section', 'ID_Section');
    }

    ######################## end relations ##################

}
