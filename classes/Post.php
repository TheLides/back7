<?php

include_once 'services/validation.php';

class Post
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


class UserPostViewModel
{
    public $Id;
    public $UserId;
    public $Text;
    public $Date;

    public function __construct($id, $userId, $text, $date)
    {
        if (!validateNumber($id)) {
            throw new Exception('Smth went wrong');
        } else {
            $this->Id = $id;
        }
        if (!validateNumber($userId)) {
            throw new Exception('Smth went wrong');
        } else {
            $this->UserId = $userId;
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

class PostViewModel
{
    public $Id;
    public $Text;
    public $Date;

    public function __construct($id, $text, $date)
    {
        if (!validateNumber($id)) {
            throw new Exception('Smth went wrong');
        } else {
            $this->Id = $id;
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

class PostUserViewModel
{
    public $Text;
    public $Date;

    public function __construct($text, $date)
    {
        if (!validateString($text)) {
            throw new Exception('Smth went wrong1');
        } else {
            $this->Text = $text;
        }
        if (!validateString($date)) {
            throw new Exception('Smth went wrong2');
        } else {
            $this->Date = $date;
        }
    }
}