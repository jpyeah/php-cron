<?php
/**
 * Created by PhpStorm.
 * User: zhangjincheng
 * Date: 17-7-24
 * Time: 下午2:40
 */

namespace Server\Components\GrayLog;


class GrayLogHelp
{
    public static function init()
    {
        //开启一个UDP用于发graylog
        if (get_instance()->config->get('log.active') == 'graylog') {
            $udp_port = get_instance()->server->listen(get_instance()->config['tcp']['socket'], get_instance()->config['log']['graylog']['udp_send_port'], SWOOLE_SOCK_UDP);
            $udp_port->on('packet', function () {
            });
        }
    }

}