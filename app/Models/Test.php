<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;
    
    protected $table = 'tests';

    protected $primaryKey = 'ID_Test';
    protected $fillable = [
        'ID_Test',
        'Tested_Pep',
        'Section',
        'Notes',
        'Mistakes',
        'Notices',
        'Duration',
        'Record_URL',
        'Mistake',
        'Rate',
        'Season',
        'State',
        'Tajweed',
        'Tester_Per',
        'Checked',
        'Questions_Number',
        'Mistakes_Number',
        'Mark'
    ];

    ######################## Begin relations ##################

    public function student(){
        return $this -> belongsTo('App\Models\User', 'Tested_Pep');
    }

    public function section(){
        return $this -> belongsTo('App\Models\Section', 'Section');
    }

    public function tester(){
        return $this -> belongsTo('App\Models\Permission', 'Tester_Per');
    }

    public function Tester_Per(){
        return $this -> belongsTo('App\Models\User', 'Tester_Per');

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
        return $this -> belongsTo('App\Models\Mistake', 'Mistake');
    }

    public function rate(){
        return $this -> belongsTo('App\Models\Rate', 'Rate');
    }
    
    ######################## end relations ##################

}
