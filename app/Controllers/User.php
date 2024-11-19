<?php

namespace App\Controllers;


use CodeIgniter\Controller;
use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use Config\Database;
class User extends BaseController
{
    public function login()
    {
        $data = [];

        if ($this->request->getMethod() == 'POST') {
            $rules = [
                'email' => 'required|min_length[6]|max_length[50]|valid_email',
                'password' => 'required|min_length[8]|max_length[255]|validateUser[email,password]',
            ];

            $errors = [
                'password' => [
                    'validateUser' => "Incorrect email or password",
                ],
            ];

            if (!$this->validate($rules, $errors)) {
                return view('login', [
                    "validation" => $this->validator,
                ]);
            } else {
                $model = new UserModel();

                $user = $model->where('email', $this->request->getVar('email'))
                    ->first();

                // Storing session values
                $this->setUserSession($user);

                if($user['privilege']=="Admin"){
                    return redirect()->to(base_url('dashboard'));
                }else{
                    return redirect()->to(base_url('homepage'));
                }
            }
        }else{
            return view('login');
        }
    }
    private function setUserSession($user)
    {
        $data = [
            'id' => $user['id'],
            'name' => $user['name'],
            'phone_no' => $user['phone_no'],
            'email' => $user['email'],
            'isLoggedIn' => true,
            'privilege'=>$user['privilege'],
        ];

        session()->set($data);
        return true;
    }

    public function register()
    {
        $data = [];
        
        if ($this->request->getMethod() == 'POST') {
            $rules = [
                'name' => 'required|min_length[3]|max_length[20]',
                'phone_no' => 'required|min_length[9]|max_length[20]',
                'email' => 'required|min_length[6]|max_length[50]|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[8]|max_length[255]',
                'password_confirm' => 'matches[password]',
            ];

            if (!$this->validate($rules)) {
                return view('register', [
                    "validation" => $this->validator,
                ]);

            } else {
                $model = new UserModel();

                // Count the total number of records in the table
                $totalRecords = $model->countAll();
                
                // Prepare the base data
                $newData = [
                    'name' => $this->request->getVar('name'),
                    'phone_no' => $this->request->getVar('phone_no'),
                    'email' => $this->request->getVar('email'),
                    'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
                ];
                
                // Add privilege if there are existing records
                if ($totalRecords == 0) {
                    $newData['privilege'] = "Admin";
                }else{
                    $newData['privilege'] = "User";
                }
                
                // Save the data
                $model->save($newData);
                
                // Set success message and redirect
                $session = session();
                $session->setFlashdata('success', 'Successful Registration');
                return redirect()->to(base_url(''));
            }
        }else{
        return view('register');
        }
    }

    public function profile()
    {
        $data = [];
        $model = new UserModel();

        $data['user'] = $model->where('id', session()->get('id'))->first();
        return view('profile', $data);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('login');
    }


    
}