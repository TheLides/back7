<?php

class User
{
    public $Name;

    public function setName($name)
    {
        if (!is_string($name)) {
            return false;
        } else {
            return true;
        }
    }

    public $Surname;

    public function setSurname($surname)
    {
        if (!is_string($surname)) {
            return false;
        } else {
            return true;
        }
    }

    public $Username;

    public function setUsername($username)
    {
        if (!is_string($username)) {
            return false;
        } else {
            return true;
        }
    }

    public $Password;

    public function setPassword($pwd)
    {
        if (!is_string($pwd)) {
            return false;
        } else {
            return true;
        }
    }

    public $Birthday;

    public function setBirthday($birthday)
    {
        if (!is_string($birthday)) {
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
        $this->Id = $id;
        $this->Name = $name;
        $this->Surname = $surname;
        $this->Status = $status;
        $this->City = $city;
    }
}

class UserAdminViewModel{
    public $Id;
    public $Name;
    public $Surname;
    public $Status;
    public $Birthday;
    public $Role;

    public function __construct($id, $name, $surname, $status, $birthday, $role){
        $this->Id = $id;
        $this->Name = $name;
        $this->Surname = $surname;
        $this->Status = $status;
        $this->Birthday = $birthday;
        $this->Role = $role;
    }
}

class UserViewModel{
    public $Id;
    public $Name;
    public $Surname;
    public $Status;

    public function __construct($id, $name, $surname, $status){
        $this->Id = $id;
        $this->Name = $name;
        $this->Surname = $surname;
        $this->Status = $status;
    }
}

class UserForAdminViewModel{
    public $Id;
    public $Name;
    public $Surname;
    public $Status;
    public $Birthday;
    public $Role;

    public function __construct($id, $name, $surname, $status, $birthday, $role){
        $this->Id = $id;
        $this->Name = $name;
        $this->Surname = $surname;
        $this->Status = $status;
        $this->Birthday = $birthday;
        $this->Role = $role;
    }
}
