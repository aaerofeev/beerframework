<?php
class Captcha
{
    /**
     * Показать каптчу
     *
     * @static
     * @return string
     */
    public static function draw()
    {
        return require(LIB_PATH . 'captcha/code.php');
    }

    /**
     * @static
     * @param $code
     * @return bool
     */
    public static function validate($code)
    {
        $captCode = Arr::get($_SESSION, 'code');

        return ($code == $captCode);
    }
}
