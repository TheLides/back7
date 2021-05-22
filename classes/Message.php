<?php

class Message
{
    public $Text;

    public function setText($text)
    {
        if (!is_string($text) || $text == "") {
            return false;
        } else {
            return true;
        }
    }
}

class MessageViewModel
{
    public $UserOwnerId;
    public $UserAimId;
    public $Text;
    public $Date;

    public function __construct($userownerid, $useraimid, $text, $date)
    {
        $this->UserAimId = $useraimid;
        $this->UserOwnerId = $userownerid;
        $this->Text = $text;
        $this->Date = $date;
    }
}
