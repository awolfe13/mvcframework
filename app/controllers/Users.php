<?php 

class Users extends Controller {
  //get the user model
  public function __construct() {
    $this->userModel = $this->model('User');
  }

  //same name as file
  public function login() {
    //find data from db and pass to view
    $data = [
      'title' => 'Login page'
    ];
    
    $this->view('users/login', $data);
  }
  
}