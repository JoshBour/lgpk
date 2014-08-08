<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 8/2/14
 * Time: 4:28 PM
 */

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Referral
 * @package Admin\Entity
 * @ORM\Entity
 * @ORM\Table(name="referrals_visitors")
 */
class ReferralVisitor {

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Referral")
     * @ORM\JoinColumn(name="referral_name", referencedColumnName="name")
     */
    private $referral;

    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    private $ip;

    public function __construct($referral, $ip){
        $this->referral = $referral;
        $this->ip = $ip;
    }

    /**
     * @param mixed $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }


} 