<?php

namespace AimBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Letters
 *
 * @ORM\Table(name="letters")
 * @ORM\Entity
 */
class Letters
{
    /**
     * @var string
     *
     * @ORM\Column(name="from_whom", type="string", length=255)
     */
    public $fromWhom;
    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    public $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="destination", type="string", length=255, nullable=true)
     */
    public $destination;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255, nullable=true)
     */
    public $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", length=65535, nullable=true)
     */
    public $message;

    /**
     * @var integer
     *
     * @ORM\Column(name="success", type="integer", length=1)
     */
    public $success;

    /**
     * @var integer
     *
     * @ORM\Column(name="sent", type="integer", length=1)
     */
    public $sent;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public $id;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get from_whom
     *
     * @return string
     */
    public function getFromWhom()
    {
        return $this->fromWhom;
    }

    /**
     * Sets the from_whom.
     *
     * @param string $fromWhom
     *
     * @return self
     */
    public function setFromWhom($fromWhom)
    {
        $this->fromWhom = $fromWhom;

        return $this;
    }

    /**
     * Get user_id
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Sets the user_id.
     *
     * @param integer $userid
     *
     * @return self
     */
    public function setUserid($userid)
    {
        $this->userId = $userid;

        return $this;
    }

    /**
     * Get destination
     *
     * @return string
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * Sets the destination.
     *
     * @param string $destination
     *
     * @return self
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Sets the subject.
     *
     * @param string $subject
     *
     * @return self
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Sets the message.
     *
     * @param string $message
     *
     * @return self
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Get success
     *
     * @return integer
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * {@inheritdoc}
     */
    public function setSuccess($success)
    {
        $this->success = $success;
        return $this;
    }

    /**
     * Get sent
     *
     * @return integer
     */
    public function getSent()
    {
        return $this->sent;
    }

    /**
     * {@inheritdoc}
     */
    public function setSent($sent)
    {
        $this->sent = $sent;
        return $this;
    }


    public function getData($post)
    {
        $vars = array();
        foreach($post as $key=>$val){
            foreach ($val as $k=>$v){
                $vars[$key][$k] = addslashes($v);
                $vars[$key][$k] = htmlspecialchars($v);
                $vars[$key][$k] = stripslashes($v);
                $vars[$key][$k] = stripslashes($v);
                $vars[$key][$k] = trim($v);
                $vars[$key][$k] = strip_tags($v);

                if (empty($v)) {
                    $vars['error'] = 'yes';
                    $vars['error1'] = 'Empty or wrong ...!';
                }
            }
        }
        return $vars;
    }

}
