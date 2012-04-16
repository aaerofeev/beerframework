<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ubuntu
 * Date: 4/11/12
 * Time: 4:39 PM
 * To change this template use File | Settings | File Templates.
 */
class Controller_Captcha extends Controller_Page
{
    public function actionIndex() {
        Captcha::draw();
    }
}
