<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'First_Name',
        'Last_Name',
        'Mid_Name',
        'Additional_Name',
        'Birth_Date',
        'Birth_Place',
        'Email',
        'Image_URL',
        'Distinguishing_Signs',
        'FingetPrinte_identity',
        'Note',
        'State_ID_State',
        'Job_ID_Job',
        'Education_ID_Education',
        'Address_ID_Address',
        'Mothers_ID_Mother',
        'Kins_ID_Kin',
        'Fathers_ID_Father',
        'Phone_Number_Call',
        'Phone_Number_Social',
        'Points',
        'Memoraization',
        'updated_at',
        'created_at',
        'UserName',
        'Password',
        'Temp_Points',
        'Token',
        'created_by'
    ];

    public function getAuthPassword()
    {
        return $this->Password;
    }
    
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * Get the password for the user.
     *
     * @return string
     */


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */

    
    protected $table = 'people';

    protected $primaryKey = 'ID_Person';
    protected $casts = [
      //  'email_verified_at' => 'datetime',
    ];

         ######################## Begin relations ##################
    
         public function address(){
            return $this -> belongsTo('App\Models\Address', 'Address_ID_Address');
        }
    
        public function mother(){
            return $this -> belongsTo('App\Models\Mother', 'Mothers_ID_Mother');
        }
    
        public function father(){
            return $this -> belongsTo('App\Models\Father', 'Fathers_ID_Father');
        }
    
        public function kin(){
            return $this -> belongsTo('App\Models\Kin', 'Kins_ID_Kin');
        }
    
        public function education(){
            return $this -> belongsTo('App\Models\Education', 'Education_ID_Education');
        }
    
        public function job(){
            return $this -> belongsTo('App\Models\Job', 'Job_ID_Job');
        }
    
        public function state(){
            return $this -> belongsTo('App\Models\State', 'State_ID_State');
        }

        public function call_phone(){
            return $this -> belongsTo('App\Models\Phone', 'Phone_Number_Call');
        }
        
        public function social_phone(){
            return $this -> belongsTo('App\Models\Phone', 'Phone_Number_Social');
        }

        public function permission(){
            return $this -> hasOne('App\Models\Permission', 'ID_Permission_Pep', 'ID_Person');
        }

        public function student(){
            return $this -> hasOne('App\Models\Student', 'ID_Student_Pep', 'ID_Person');
        }
        
        public function tests(){
            return $this -> hasMany('App\Models\Test', 'Tested_Pep', 'ID_Person');
        }

        public function additional_poits(){
            return $this -> hasMany('App\Models\Additional_Poit', 'Receiver_Pep', 'ID_Person');
        }

    ######################## end relations ##################

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    
     public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    
}
