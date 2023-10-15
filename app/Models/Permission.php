<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $table = 'permission';

    protected $primaryKey = 'ID_Permission_Pep';
    protected $fillable = [
        'ID_Permission_Pep',
        'Admin',
        'Manager',
        'Supervisor',
        'Moderator',
        'Assisstent',
        'Custom',
        'Reciter',
        'Tester',
        'Seller',
        'Appoint',
        'View_Person',
        'Add_Person',
        'Edit_Person',
        'Delete_Person',
        'View_Group',
        'Add_Group',
        'Delete_Group',
        'Observe',
        'Recite',
        'Test',
        'Sell',
        'Attendance',
        'Evaluation',
        'Level',
        'Note',
        'State',
        'View_Log',
        'Edit_Group',
        'Appoint_Student',
        'Adder',
        'View_People',
        'View_Attendance',
        'View_Recite',
        'View_Groups'
    ];

    ######################## Begin relations ##################

    public function state(){
        return $this -> belongsTo('App\Models\State', 'State');
    }

    public function user(){
        return $this -> belongsTo('App\Models\User', 'ID_Permission_Pep');
    }

    public function supervisor_groups(){
        return $this -> hasMany('App\Models\Group', 'Supervisor', 'ID_Permission_Pep');
    }

    public function moderator_groups(){
        return $this -> hasMany('App\Models\Group', 'Moderator', 'ID_Permission_Pep');
    }

    public function assistant_groups(){
        return $this -> hasMany('App\Models\Groups_has_Assistant', 'Assistants', 'ID_Permission_Pep');
    }

    public function tests(){
        return $this -> hasMany('App\Models\Test', 'Tester_Per', 'ID_Permission_Pep');
    }
    
    public function points(){
        return $this -> hasMany('App\Models\Additional_Poit', 'Sender_Per', 'ID_Permission_Pep');
    }

    public function recites(){
        return $this -> hasMany('App\Models\Reciting', 'Listner_Per', 'ID_Permission_Pep');
    }
    
    ######################## end relations ##################

}
