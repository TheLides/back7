<?php

class Post
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


class UserPostViewModel
{
    public $UserId;
    public $Text;
    public $Date;

    public function __construct($userId, $text, $date)
    {
        $this->UserId = $userId;
        $this->Text = $text;
        $this->Date = $date;
    }
}

class PostViewModel
{
    public $Text;
    public $Date;

    public function __construct($text, $date)
    {
        $this->Text = $text;
        $this->Date = $date;
    }
}