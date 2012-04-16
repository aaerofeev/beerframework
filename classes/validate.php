<?php
/**
 * Created by JetBrains PhpStorm.
 * User: comment
 * Date: 4/13/12
 * Time: 10:17 AM
 * To change this template use File | Settings | File Templates.
 */
class Validate
{
    public static function viewErrors($errors)
    {
        return View::factory('system/errors', array('errors' => $errors));
    }
}
