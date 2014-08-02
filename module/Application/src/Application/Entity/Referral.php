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
 * @ORM\Entity(repositoryClass="Application\Repository\ReferralRepository")
 * @ORM\Table(name="referrals")
 */
class Referral {

    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", length=11)
     */
    private $views;

    /**
     * @ORM\OneToMany(targetEntity="ReferralVisitor", mappedBy="referral")
     */
    private $visitors;

    public function __construct($name, $views){
        $this->name = $name;
        $this->views = $views;
        $this->visitors = new ArrayCollection();
    }

    public function increaseViews(){
        $this->views++;
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

    /**
     * @param mixed $views
     */
    public function setViews($views)
    {
        $this->views = $views;
    }

    /**
     * @return mixed
     */
    public function getViews()
    {
        return $this->views;
    }

    public function addVisitors($visitors){
        if(is_array($visitors)){
            foreach($visitors as $visitor)
                $this->visitors->add($visitor);
        }else{
            $this->visitors->add($visitors);
        }
    }

    /**
     * @param mixed $visitors
     */
    public function setVisitors($visitors)
    {
        $this->visitors = $visitors;
    }

    /**
     * @return mixed
     */
    public function getVisitors()
    {
        return $this->visitors;
    }


} 