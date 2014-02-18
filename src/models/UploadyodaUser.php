<?php namespace Quasimodal\Uploadyoda;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

use Eloquent;

class UploadyodaUser extends Eloquent implements UserInterface, RemindableInterface
{
    protected $table = 'uploadyoda_users';

    protected $fillable = array( 'firstname', 'lastname', 'email', 'password' );

    public $rules = array(
        'firstname'=>'required|alpha|min:2',
        'lastname'=>'required|alpha|min:2',
        'email'=>'required|email|unique:uploadyoda_users',
        'password'=>'required|alpha_num|between:6,12|confirmed',
        'password_confirmation'=>'required|alpha_num|between:6,12'
    );

    /**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password');

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}

    public function setPassword($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
}
