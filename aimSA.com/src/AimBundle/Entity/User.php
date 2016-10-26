<?php
/**
 * Created by PhpStorm.
 * User: Palamarchuk Vladymir
 * Date: 04.10.16
 * Time: 13:04
 */

// src/AimBundle/Entity/User.php
namespace AimBundle\Entity;

use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\ExclusionPolicy;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table("fos_user")
 * @ORM\Entity
 * @ExclusionPolicy("all")
 */

class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

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
     * Get the formatted name to display (NAME Username or username)
     *
     * @param $separator: the separator between name and firstname (default: ' ')
     * @return String
     * @VirtualProperty
     */
    public function getUsedName($separator = ' '){
        if($this->getUsername()!=null){
            return ucfirst(strtolower($this->getUsername()));
        }
        else{
            return $this->getUsername();
        }
    }
    
}