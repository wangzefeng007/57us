<?php

/**
 * @desc  常用旅客表
 * Class  MemberPassengerModule
 */
class MemberPassengerModule extends CommonModule{
    public function __construct() {
        $this->TableName = 'member_passenger';
        $this->KeyID = 'PassengerID';
    }
}