<?php

use Phalcon\Mvc\Model;

class User extends Model
{
	public function initialize()
    {
        $this->belongsTo("RoleID", "Role", "ID", array(
            "foreignKey" => true
            ));

        $this->hasMany("ID", "Image", "UserID");
    }

    public function getUsedSpace()
    {
        // Missing `sum` function in Model_Resultset_Simple class
        // return $this->Image->sum(array('column' => 'FileSize'));
        $usedSpace = 0;
        foreach ($this->Image as $key => $image) {
            $usedSpace += $image->FileSize;
        }
        return $usedSpace;
    }

    public function getDiskSpace()
    {
        return $this->role->DiskSpace;
    }
}