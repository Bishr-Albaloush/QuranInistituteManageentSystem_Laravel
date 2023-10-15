<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $primaryKey = 'ID_Page';
    protected $table = 'pages';
    protected $fillable = [
        'ID_Page',
        'Page_Number',
        'Surah',
        'Sections_ID_Section',
        'Verses_ID_Verse_First',
        'Verses_ID_Verse_Last'
    ];

    ######################## Begin relations ##################

    public function section(){
        return $this -> belongsTo('App\Models\Section', 'Sections_ID_Section');
    }

    public function recitings(){
        return $this -> hasMany('App\Models\Reciting', 'Page', 'ID_Page');
    }

    ######################## end relations ##################

}
