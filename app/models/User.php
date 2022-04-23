<?php

//single User in model
  class User {
    private $db;
    
    //instantiate db
    public function __construct() {
      $this->db = new Database;
    }

    //Register the user to DB
    public function register($data) {
      $this->db->query("INSERT INTO users (username, email, password) 
      VALUES(:username, :email, :password)");

      //Bind Values
      $this->db->bind(':username', $data['username']);
      $this->db->bind(':email', $data['email'] );
      $this->db->bind(':password', $data['password']);

      //execute function
      if ($this->db->execute()) {
        return true;
      } else {
        false;
      }
    }

    public function login($username, $password) {
        $this->db->query("SELECT * FROM users where username = :username");

        //bind value
        $this->db->bind(':username', $username);
        $row = $this->db->single();

        $hashedPassword = $row->password;
        if (password_verify($password, $hashedPassword)) {
            return $row;
        } else {
            false;
        }
    }
    
    //Find user by email, email passed in from controller
    public function findUserByEmail($email) {
      $this->db->query("SELECT * FROM users WHERE email = :email");
      
      //email param binded with email variable
      $this->db->bind(':email', $email);

      //check if email is already registered (row count will be 0)
      if($this->db->rowCount() > 0) {
        return true;
      } else {
        return false;
      }
    }

    public function getUsers() {
      $this->db->query("SELECT * FROM users");

      $result = $this->db->resultSet();

      return $result;
    }
  }