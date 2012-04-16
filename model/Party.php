<?php

class Party extends ActiveRecord\Model
{
    static $table_name = 'party';

    static $validates_presence_of = array(
        array('fio'),
        array('description'),
        array('class'),
        array('image', 'on' => 'create'),
    );

    public function getClass()
    {
        $lb = '6-7';

        if ($this->class == 2) $lb = '8-9';
        if ($this->class == 3) $lb = '10-11';

        return $lb . ' класс';
    }
}
