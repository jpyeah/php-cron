<?php
/**
 * Created by PhpStorm.
 * User: zhangjincheng
 * Date: 16-7-15
 * Time: 下午4:49
 */
$config['mysql']['enable'] = true;
$config['mysql']['active'] = 'test';
$config['mysql']['test']['host'] = '192.168.1.24';
$config['mysql']['test']['port'] = '3306';
$config['mysql']['test']['user'] = 'root';
$config['mysql']['test']['password'] = '123';
$config['mysql']['test']['database'] = 'bibitest';
$config['mysql']['test']['charset'] = 'utf8';
$config['mysql']['asyn_max_count'] = 10;
return $config;