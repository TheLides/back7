<?php
include_once 'services/validation.php';

class Message
{
    public $Text;

    public function setText($text)
    {
        if (!validateString($text)) {
            return false;
        } else {
            return true;
        }
    }
}

class MessageViewModel
{
    public $Id;
    public $UserOwnerId;
    public $UserAimId;
    public $Text;
    public $Date;

    public function __construct($id, $userownerid, $useraimid, $text, $date)
    {
        if (!validateNumber($id)) {
            throw new Exception('Smth went wrong');
        } else {
            $this->Id = $id;
        }
        if (!validateNumber($userownerid)) {
            throw new Exception('Smth went wrong');
        } else {
            $this->UserOwnerId = $userownerid;
        }
        if (!validateNumber($useraimid)) {
            throw new Exception('Smth went wrong');
        } else {
            $this->UserAimId = $useraimid;
        }

        if (!validateString($text)) {
            throw new Exception('Smth went wrong');
        } else {
            $this->Text = $text;
        }
        if (!validateString($date)) {
            throw new Exception('Smth went wrong');
        } else {
            $this->Date = $date;
        }
    }
}
