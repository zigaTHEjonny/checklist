<?php

namespace App\Controllers;

use App\Models\UserModel;

class Users extends BaseController
{
    public function index(): string
    {
        return view('welcome_message');
    }

    public function register()
    {
        $data = [
            'title' => 'Register',
        ];
        helper(['form']);
       //die($this->request->getMethod());
        if ($this->request->getMethod() == 'POST') {
            //todo validation
            //print_r("here");
            $rules = [
                'name' => 'required|min_length[3]|max_length[50]',
                'email' => 'required|min_length[6]|max_length[50]|valid_email|is_unique[user.email]',
                'password' => 'required|min_length[8]|max_length[50]',
                'password_confirm' => 'matches[password]',
            ];
            
            if (!$this->validate($rules)) {
                $data['validation'] = $this->validator;
                //print_r($data['validation']);
            } else {
                $model = new UserModel();

                $newData = [
                    'name' => $this->request->getVar('name'),
                    'email' => $this->request->getVar('email'),
                    'password' => $this->request->getVar('password'),
                ];
                //print_r($newData);
                //die();
                $model->save($newData);

                $session = session();
                $session->setFlashdata('success', 'Successful Registration');

                return redirect()->to('/login');
            }

        }


        return view('templates/register', $data);
    }

    
    public function login()
    {
        $data = [
            'title' => 'Login',
        ];
        helper(['form']);
       //die($this->request->getMethod());
        if ($this->request->getMethod() == 'POST') {
            //todo validation
            //print_r("here");
            $rules = [
                'email' => 'required|valid_email',
                'password' => 'required|validateUser[email,password]',
            ];

            $errors = [
                'password' => [
                    'validateUser' => 'Email or password incorrect',
                ]
            ];
            
            if (!$this->validate($rules, $errors)) {
                $data['validation'] = $this->validator;
                //print_r($data['validation']);
            } else {
                $model = new UserModel();
                $user = $model->where('email', $this->request->getVar('email'))->first();

                $this->setUserSession($user);

                return redirect()->to('/');
            }

        }


        return view('templates/login', $data);
    }

    
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');

    }

    private function setUserSession($user) {
        $data = [
            'id' => $user['user_id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'isLoggedIn' => true,
        ];

        session()->set($data);

        return true;
    }
}
