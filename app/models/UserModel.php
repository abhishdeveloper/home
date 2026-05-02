<?php

class UserModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Find user by email
    public function findUserByEmail($email) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        return $this->db->rowCount() > 0 ? $row : false;
    }

    // Register user
    public function register($data) {
        $this->db->query('INSERT INTO users (name, email, password, role_id) VALUES(:name, :email, :password, :role_id)');

        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']); // Expected to be already hashed
        $this->db->bind(':role_id', $data['role_id']);

        return $this->db->execute();
    }

    // Login user
    public function login($email, $password) {
        $row = $this->findUserByEmail($email);

        if (!$row || empty($row->password)) {
            return false;
        }

        $hashed_password = $row->password;
        if (password_verify($password, $hashed_password)) {
            return $row;
        } else {
            return false;
        }
    }

    // Find user by Google ID
    public function findUserByGoogleId($google_id) {
        $this->db->query('SELECT * FROM users WHERE google_id = :google_id');
        $this->db->bind(':google_id', $google_id);

        $row = $this->db->single();

        return $this->db->rowCount() > 0 ? $row : false;
    }

    // Register Google User (Initial Step without Role)
    // Returns the new user ID
    public function registerGoogleUser($name, $email, $google_id) {
        // We will insert with a temporary dummy role or null if role_id allowed null.
        // Assuming role_id must be set, let's just default to 3 (Patient) and let them change it,
        // OR better yet, we will force an update before their session is considered fully authenticated.

        // Actually, we'll assign role 3 by default, and update it later in the setup form.
        $this->db->query('INSERT INTO users (name, email, google_id, role_id) VALUES(:name, :email, :google_id, 3)');

        $this->db->bind(':name', $name);
        $this->db->bind(':email', $email);
        $this->db->bind(':google_id', $google_id);

        if($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    // Update Role
    public function updateRole($user_id, $role_id) {
        $this->db->query('UPDATE users SET role_id = :role_id WHERE id = :id');
        $this->db->bind(':role_id', $role_id);
        $this->db->bind(':id', $user_id);

        return $this->db->execute();
    }

    // Find User By ID
    public function findUserById($id) {
        $this->db->query('SELECT * FROM users WHERE id = :id');
        $this->db->bind(':id', $id);

        $row = $this->db->single();
        return $this->db->rowCount() > 0 ? $row : false;
    }

    // Link Google Account
    public function linkGoogleAccount($user_id, $google_id) {
        $this->db->query('UPDATE users SET google_id = :google_id WHERE id = :id');
        $this->db->bind(':google_id', $google_id);
        $this->db->bind(':id', $user_id);

        return $this->db->execute();
    }
}
