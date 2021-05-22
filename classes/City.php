<?php
class City {
    public $Name;

    public function setName($name)
    {
        if (!is_string($name) || $name == "") {
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
        $this->Id = $id;
        $this->Name = $name;
    }
}
