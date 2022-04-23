<?php

class Users extends Controller
{
    //get the user model
    public function __construct()
    {
        $this->userModel = $this->model('User');
    }

    public function register()
    {
        $data = [
            'username' => '',
            'email' => '',
            'password' => '',
            'confirmPassword' => '',
            'usernameError' => '',
            'emailError' => '',
            'passwordError' => '',
            'confirmPasswordError' => '',
        ];

        //if submit is equal to post or a get request
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            //Sanitize post data, uncoded unwanted characters
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            //trim removes white space on both ends
            $data = [
                'username' => trim($_POST['username']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'confirmPassword' => trim($_POST['confirmPassword']),
                'usernameError' => '',
                'emailError' => '',
                'passwordError' => '',
                'confirmPasswordError' => '',
            ];

            //validation username for letters and numbers
            $nameValidation = "/^[a-zA-Z0-9]*$/";
            $passwordValidation = "/^(.{0,7}|[^a-z]*|[^\d]$/i";

            
            if (empty($data['username'])) {
              $data['usernameError'] = 'Please enter a name.';
            } elseif (!preg_match($nameValidation, $data['username'])) {
              $data['usernameError'] = 'Username can only contain leters and numbers.';
            }

            //Validation email
             if (empty($data['email'])) {
              $data['emailError'] = 'Please enter an email address.';
              //validates if correct email (no weird characters)
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
              $data['emailError'] = 'Please enter the correct email format';
              //check if email exists
            } else {
              if ($this->userModel->findUserByEmail($data['email'])) {
                $data['emailError'] = 'Email is alrelady taken';
              }
            }

            //Valdiate Password on length and numeric values
            if (empty($data['password'])) {
              $data['passwordError'] = "Please enter password.";
            } elseif (strlen($data['password'] < 6)) {
              $data['passwordError'] = "Password msut be at least 8 chatacters";
            } elseif (!preg_match($passwordValidation, $data['password'])) {
              $data['passwordError'] = 'Password must have at least one numeric value';
            }

            //Validate Confirm Password
             if (empty($data['confirmPassword'])) {
              $data['confirmPasswordError'] = "Please confirm password";
            } else {
              if ($data['password'] != $data['confirmPassword']) {
                $data['confirmPasswordError'] = "Passwords do not match, please try again.";
              }
            }

            //Make sure errors are empty
            if (empty($data['usernameError']) && empty($data['emailError']) && 
            empty($data['passwordError']) && empty($data['confirmPasswordError'])) {
              
              //Hash password
              $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

              //Register user from model function
              if ($this->userModel->register($data)) {
                //redirect to login page
                header('location: ' . URLROOT . '/users/login');
              } else {
                die('Something went wrong');
              }
            }     
        }

        $this->view('users/register', $data);
    }

    //same name as file
    public function login()
    {
        //find data from db and pass to view
        $data = [
            'title' => 'Login page',
            'usernameError' => '',
            'passwordError' => '',
        ];

        $this->view('users/login', $data);
    }
}