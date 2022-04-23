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
            $passwordValidation = "/^(.{0,7}|[^a-z]*|[^\d]*)$/i";

            
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
            if(empty($data['password'])){
                $data['passwordError'] = 'Please enter password.';
            } elseif(strlen($data['password']) < 6){
                $data['passwordError'] = 'Password must be at least 8 characters';
            } elseif (preg_match($passwordValidation, $data['password'])) {
                $data['passwordError'] = 'Password must be have at least one numeric value.';
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
            'username' => '',
            'password' => '',
            'usernameError' => '',
            'passwordError' => '',
        ];

        //check for Post method
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            //Sanitize post data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $data = [
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password']),
                'usernameError' => '',
                'passwordError' => '',
            ];

            //validate username
            if (empty($data['username'])) {
                $data['usernameError'] = "Please enter a username";
            }

            //validate password
            if (empty($data['password'])) {
                $data['passwordError'] = "Please enter a password";
            }

            //check if all errors are empty
            if (empty($data['usernameError']) && empty($data['passwordError'])) {
                $loggedInUser = $this->userModel->login($data['username'], $data['password']);

                if ($loggedInUser) {
                    $this->createUserSession($loggedInUser);
                } else {
                    $data['passwordError'] = "Username or Password is incorrect, please try again";

                    $this->view('users/login', $data);
                }
            }

        } else {
            $data = [
                'username' => '',
                'password' => '',
                'usernameError' => '',
                'passwordError' => '',
            ];
        }

        $this->view('users/login', $data);
    }

    public function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['email']);
        header('location: ' . URLROOT . '/users/login');
    }

    public function createUserSession($user) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['username'] = $user->username;
        $_SESSION['email'] = $user->email;
        header('location: ' . URLROOT . '/pages/index');
    }

}