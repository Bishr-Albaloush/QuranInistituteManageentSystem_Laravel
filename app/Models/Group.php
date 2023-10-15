<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $primaryKey = 'ID_Group';
    protected $fillable = [
        'ID_Group',
        'Group_Name',
        'Create_Date',
        'Private_Meeting',
        'Supervisor',
        'Moderator',
        'Class',
        'State'
        
    ];

    ######################## Begin relations ##################

    public function supervisor(){
        return $this -> belongsTo('App\Models\Permission', 'Supervisor');
    }

    public function moderator(){
        return $this -> belongsTo('App\Models\Permission', 'Moderator');
    }


    public function assistants(){
        return $this -> hasMany('App\Models\Groups_has_Assistant', 'Group', 'ID_Group');
    }

    public function Students(){
        return $this -> hasMany('App\Models\Student', 'Group', 'ID_Group');
    }

    
    
    ######################## end relations ##################
}
