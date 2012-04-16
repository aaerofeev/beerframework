<?php

$databaseConfig = Config::load('database');

$cfg = ActiveRecord\Config::instance();
$cfg->set_model_directory(ROOT_PATH . 'model');
$select = $databaseConfig['select'];
unset($databaseConfig['select']);
$cfg->set_connections($databaseConfig);
$cfg->set_default_connection($select);