<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 8/2/14
 * Time: 4:28 PM
 */

namespace Application\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Referral
 * @package Application\Entity
 * @ORM\Entity
 * @ORM\Table(name="referral_applications")
 */
class ReferralApplication {

    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="smallint", length=1)
     */
    private $accepted;

    public function __construct($email, $accepted){
        $this->email = $email;
        $this->accepted = $accepted;
    }

    /**
     * @param mixed $accepted
     */
    public function setAccepted($accepted)
    {
        $this->accepted = $accepted;
    }

    /**
     * @return mixed
     */
    public function getAccepted()
    {
        return $this->accepted;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }



} 