<?php

use Phalcon\Mvc\Model;

class User extends Model
{
	public function initialize()
    {
        $this->belongsTo("RoleID", "Role", "ID", array(
            "foreignKey" => true
        ));
    }
}