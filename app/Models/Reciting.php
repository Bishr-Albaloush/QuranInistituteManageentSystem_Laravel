<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reciting extends Model
{
    use HasFactory;

    protected $table = 'recitings';

    protected $primaryKey = 'ID_Recting';
    protected $fillable = [
        'ID_Recting',
        'Reciter_Pep',
        'Page',
        'First_Verse',
        'Last_Verse',
        'Notes',
        'Mistakes',
        'Notices',
        'Duration',
        'Record_URL',
        'Mistakes_ID_Mistake',
        'Rates_ID_Rate',
        'Season',
        'State',
        'Tajweed',
        'Listner_Per',
        'Checked',
    ];

    ######################## Begin relations ##################

    public function student(){
        return $this -> belongsTo('App\Models\Student', 'Reciter_Pep');
    }

    public function page(){
        return $this -> belongsTo('App\Models\Page', 'Page');
    }

    public function firt_verse(){
        return $this -> belongsTo('App\Models\Verse', 'First_Verse');
    }
    
    public function last_verse(){
        return $this -> belongsTo('App\Models\Verse', 'Last_Verse');
    }

    public function listner(){
        return $this -> belongsTo('App\Models\Permission', 'Listner_Per');
    }

    public function season(){
        return $this -> belongsTo('App\Models\Season', 'Season');
    }

    public function state(){
        return $this -> belongsTo('App\Models\State', 'State');
    }
    
    public function checked(){
        return $this -> belongsTo('App\Models\State', 'Checked');
    }

    public function mistakes(){
        return $this -> belongsTo('App\Models\Mistake', 'Mistakes_ID_Mistake');
    }

    public function rate(){
        return $this -> belongsTo('App\Models\Rate', 'Rates_ID_Rate');
    }
    
    ######################## end relations ##################

}
