<?php

include_once "services/validation.php";

class User
{
    public $Name;

    public function setName($name)
    {
        if (!validateString($name)) {
            return false;
        } else {
            return true;
        }
    }

    public $Surname;

    public function setSurname($surname)
    {
        if (!validateString($surname)) {
            return false;
        } else {
            return true;
        }
    }

    public $Username;

    public function setUsername($username)
    {
        if (!validateString($username)) {
            return false;
        } else {
            return true;
        }
    }

    public $Password;

    public function setPassword($pwd)
    {
        if (!validateString($pwd)) {
            return false;
        } else {
            return true;
        }
    }

    public $Birthday;

    public function setBirthday($birthday)
    {
        if (!validateString($birthday)) {
            return false;
        } else {
            return true;
        }
    }
}

class UserViewCityModel{
    public $Id;
    public $Name;
    public $Surname;
    public $Status;
    public $City;

    public function __construct($id, $name, $surname, $status, $city)
    {
        if (!validateNumber($id)){
            throw new Exception('Smth went wrong');
        }
        else {
            $this->Id = $id;
        }
        if (!validateNumber($city)){
            throw new Exception('Smth went wrong');
        }
        else {
            $this->City = $city;
        }
        if (!validateString($name)) {
            throw new Exception('Smth went wrong');
        } else {
            $this->Name = $name;
        }
        if (!validateString($surname)) {
            throw new Exception('Smth went wrong');
        } else {
            $this->Surname = $surname;
        }
        if (!validateString($status)) {
            throw new Exception('Smth went wrong');
        } else {
            $this->Status = $status;
        }
    }
}

class UserViewModel{
    public $Id;
    public $Name;
    public $Surname;
    public $Status;

    public function __construct($id, $name, $surname, $status){
        if (!validateNumber($id)){
            throw new Exception('Smth went wrong1');
        }
        else {
            $this->Id = $id;
        }
        if (!validateString($name)) {
            throw new Exception('Smth went wrong2');
        } else {
            $this->Name = $name;
        }
        if (!validateString($surname)) {
            throw new Exception('Smth went wrong3');
        } else {
            $this->Surname = $surname;
        }
        if (!validateString($status)) {
            throw new Exception('Smth went wrong4');
        } else {
            $this->Status = $status;
        }
    }
}

class UserForAdminViewModel{
    public $Id;
    public $Name;
    public $Surname;
    public $Status;
    public $Birthday;
    public $Role;
    public $postArray;

    public function __construct($id, $name, $surname, $status, $birthday, $role, $postArray){
        if (!validateNumber($id)){
            throw new Exception('Smth went wrong5');
        }
        else {
            $this->Id = $id;
        }
        if (!validateString($role)){
            throw new Exception('Smth went wrong6');
        }
        else {
            $this->Role = $role;
        }
        if (!validateString($name)) {
            throw new Exception('Smth went wrong7');
        } else {
            $this->Name = $name;
        }
        if (!validateString($surname)) {
            throw new Exception('Smth went wrong8');
        } else {
            $this->Surname = $surname;
        }
        if (!validateString($status)) {
            throw new Exception('Smth went wrong9');
        } else {
            $this->Status = $status;
        }
        if (!validateString($birthday)){
            throw new Exception('Smth went wrong0');
        } else {
            $this->Birthday = $birthday;
        }

        $this->postArray = $postArray;
    }
}
