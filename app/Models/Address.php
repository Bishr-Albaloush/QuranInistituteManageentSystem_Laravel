<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $primaryKey = 'ID_Address';
    protected $table = 'addresses';
    
    protected $fillable = [
        'City',
        'Area',
        'Street',
        'Mark'
    ];
    
          ######################## Begin relations ##################
        public function user(){
            return $this -> hasMany('App\Models\User', 'Address_ID_Address', 'ID_Address');
        }
        ######################## end relations ##################

}
