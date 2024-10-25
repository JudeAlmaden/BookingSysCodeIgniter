<?php

namespace App\Validation;
use App\Models\UserModel;
class Userrules
{
    public function validateUser(string $str, string $fields, array $data): bool
    {
        $model = new UserModel();
        $user = $model->where('email', $data['email'])->first();
    
        return $user && password_verify($data['password'], $user['password']);
    }
}
