<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
	protected $table                = 'users';
	protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $dateFormat    = 'datetime';
	protected $allowedFields        = [
		"name",
		"email",
		"phone_no",
		"password",
		"privilege"
	];


}