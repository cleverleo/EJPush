<?php

/**
 * Created by PhpStorm.
 * User: leoliang
 * Date: 14-4-21
 * Time: 下午5:23
 */
class EJPushMessage
{
    const CODE_AT = 1;
    const CODE_FEEDS_COMMENT = 2;
    const CODE_CHAT = 3;
    const CODE_GARTEN = 4;
    const CODE_UNIT = 5;
    const CODE_SCHOOL = 6;
    const CODE_NEW = 7;
    const CODE_THIRD = 8;
    const CODE_TASK = 9;
    const CODE_TASK_COMMENT = 10;

    private $_id = null;
    private $content = '';

    private $extras = array(
        'c' => 0,
        'd' => ''
    );
    private $ios = array(
        'badge' => 1
    );

    public function __construct($content = '')
    {
        $this->setContent($content);
    }

    public function getExtras()
    {
        return array_merge($this->extras, array('ios' => $this->ios));
    }

    public function getId()
    {
        if ($this->_id === null) {
            $this->_id = Yii::app()->cache->getMemCache()->increment('push_msg', 1);
            if ($this->_id === false) {
                //init
                Yii::app()->cache->getMemCache()->set('push_msg', 1, 0);
                $this->_id = 1;
            }
        }
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = $id;
    }

    public function setExtras($k, $v)
    {
        if (is_array($k)) {
            $this->extras = $k;
        } else {
            $this->extras[$k] = $v;
        }
    }

    public function setIosPrototype($k, $v)
    {
        $this->ios[$k] = $v;
    }

    public function setSilent()
    {
        $this->content = '';
        $this->setIosPrototype('content-available', 1);
        $this->setBadge(0);
    }

    public function setBadge($num = 0)
    {
        $this->setIosPrototype('badge', $num);
    }

    public function getContent()
    {
        return mb_strlen($this->content) > 40 ? (mb_substr($this->content, 0, 40, 'utf-8') . '...') : $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function setCode($code)
    {
        $this->setExtras('c', $code);
    }

    public function setData($data)
    {
        $this->setExtras('d', (string)$data);
    }
}