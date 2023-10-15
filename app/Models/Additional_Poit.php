<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Additional_Poit extends Model
{
    use HasFactory;
    protected $primaryKey = 'ID_Additional_Points';
    protected $table = 'additional_poits';
    protected $fillable = [
        'ID_Additional_Points',
        'Note',
        'Points',
        'Receiver_Pep',
        'Sender_Per',
        'Checked',
        'created_at'
    ];

    ######################## Begin relations ##################

      public function state(){
        return $this -> belongsTo('App\Models\State', 'Checked');
    }

    public function receiver(){
        return $this -> belongsTo('App\Models\User', 'Receiver_Pep');
    }

    public function sender(){
        return $this -> belongsTo('App\Models\Permission', 'Sender_Per');
    }

    ######################## end relations ##################
}
