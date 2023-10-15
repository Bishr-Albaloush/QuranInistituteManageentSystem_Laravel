<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Groups_has_Assistant extends Model
{

    protected $table = 'groups_has_assistants';
    
    protected $fillable = [
        'Group', 'Assistants',
    ];
    use HasFactory;

    ######################## Begin relations ##################

    public function assistants(){
        return $this -> belongsTo('App\Models\Permission', 'Assistants');
    }

    public function groups(){
        return $this -> belongsTo('App\Models\Group', 'Group');
    }

    ######################## end relations ##################
}
