<?php
// app/core/Controller.php

class Controller {
    // Load model
    public function model($model) {
        if(file_exists('../app/models/' . $model . '.php')){
            require_once '../app/models/' . $model . '.php';
            return new $model();
        } else {
            die("Model does not exist: " . $model);
        }
    }

    // Load view
    public function view($view, $data = []) {
        if(file_exists('../app/views/' . $view . '.php')) {
            require_once '../app/views/' . $view . '.php';
        } else {
            die("View does not exist: " . $view);
        }
    }
}
