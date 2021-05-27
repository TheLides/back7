<?php

include_once 'services/validation.php';

class City {
    public $Name;

    public function setName($name)
    {
        if (!validateString($name)) {
            return false;
        } else {
            return true;
        }
    }

}

class CityViewModel {
    public $Id;
    public $Name;

    public function __construct($id, $name)
    {
        if (!validateString($name)) {
            throw new Exception('Smth went wrong');
        } else {
            $this->Name = $name;
        }
        if (!validateNumber($id)){
            throw new Exception('Smth went wrong');
        }
        else {
            $this->Id = $id;
        }

    }
}
