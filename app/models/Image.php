<?php

use Phalcon\Mvc\Model;

class Image extends Model
{
    public function initialize()
    {
        $this->belongsTo("UserID", "User", "ID", array(
            "foreignKey" => true
        ));
    }
}