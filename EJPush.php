<?php

/**
 * Created by PhpStorm.
 * User: leoliang
 * Date: 14-6-25
 * Time: 上午12:01
 */
class EJPush extends CApplicationComponent
{
    const PLATFORM_IOS = 'ios';
    const PLATFORM_ANDROID = 'android';
    const PLATFORM_ALL = 'ios,android';

    public $key;
    public $secret;
    public $mode;

    public $clientPath = 'application.vendor.jpush.JPushClient';

    private $last_error;

    public function init()
    {
        Yii::import($this->clientPath);
        parent::init();
    }

    private function getClient($platform = self::PLATFORM_ALL)
    {
        static $client = null;

        if ($client === null) {
            $client = new JPushClient($this->key, $this->secret, 864000, $platform, $this->mode ? true : false); //10天
        }
        return $client;
    }

    private function send($params, EJPushMessage $msg)
    {
        $client = self::getClient();
        $params = array_merge(array(
            'receiver_type' => 1,
            'receiver_value' => '',
            'sendno' => $msg->getId(),
            'send_description' => '',
            'override_msg_id' => ''
        ), $params);
        $this->last_error = $client->sendNotification($msg->getContent(), $params, $msg->getExtras());

        return $this->last_error->getCode() == 0;
    }

    public function getLastErrors()
    {
        return $this->last_error;
    }

    public function sendByTag($tag, EJPushMessage $msg)
    {
        if (is_array($tag)) $tag = join(',', $tag);
        $params = array(
            'receiver_type' => 2,
            'receiver_value' => $tag,
        );

        return self::send($params, $msg);
    }

    public function sendByAlice($alice, EJPushMessage $msg)
    {
        if (is_array($alice)) $alice = join(',', $alice);
        $params = array(
            'receiver_type' => 3,
            'receiver_value' => $alice,
        );
        return self::send($params, $msg);

    }

    public function sendAll(EJPushMessage $msg)
    {
        $params = array(
            'receiver_type' => 4
        );
        return self::send($params, $msg);
    }
} 