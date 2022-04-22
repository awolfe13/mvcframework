<?php

//single User in model
  class User {
    private $db;
    
    //instantiate db
    public function __construct() {
      $this->db = new Database;
    }

    public function getUsers() {
      $this->db->query("SELECT * FROM users");

      $result = $this->db->resultSet();

      return $result;
    }
  }