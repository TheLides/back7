<?php

class Photo
{
    public $Link;

    public function setLink($link)
    {
        if (!is_string($link) || $link == "") {
            return false;
        } else {
            return true;
        }
    }
}


class PhotoViewModel
{
    public $Link;
    public $CreatorId;

    public function __construct($link, $creatorId)
    {
        $this->CreatorId = $creatorId;
        $this->Link = $link;
    }
}
