<?php namespace Quasimodal\Uploadyoda\models;

use Illuminate\Auth\UserInterface,
    Illuminate\Auth\Reminders\RemindableInterface,
    Eloquent;

class UploadyodaUser extends Eloquent implements UserInterface, RemindableInterface
{
    protected $table = 'uploadyoda_users';

    protected $fillable = array( 'firstname', 'lastname', 'email', 'password', 'activated' );

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
