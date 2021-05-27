<?php
include_once 'services/validation.php';

class PhotoViewModel
{
    public $Id;
    public $Link;
    public $CreatorId;

    public function __construct($id, $link, $creatorId)
    {
        if (!validateNumber($id)) {
            throw new Exception('Smth went wrong');
        } else {
            $this->Id = $id;
        }
        if (!validateNumber($creatorId)) {
            throw new Exception('Smth went wrong');
        } else {
            $this->CreatorId = $creatorId;
        }
       
        $this->Link = $link;

    }
}
