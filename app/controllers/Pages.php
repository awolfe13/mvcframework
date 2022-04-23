<?php

//plural class in controllers
class Pages extends Controller {
  public function __construct() {
    //instantiate new model User from models/User.php
    $this->userModel = $this->model('User');
  }

  public function index() {
    $users = $this->userModel->getUsers();
    
    $data = [
      'title' => 'Home page',
      'users' => $users
    ];
    $this->view('pages/index', $data);
  }

  public function about() {
    $this->view('pages/about');
  }
  
}