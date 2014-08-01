<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 7/28/14
 * Time: 5:42 PM
 */

namespace League\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Attribute
 * @package League\Entity
 * @ORM\Entity
 * @ORM\Table(name="attributes")
 */
class Attribute {

    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Champion", mappedBy="attributes")
     */
    private $relatedChampions;

    public function __construct(){
        $this->relatedChampions = new ArrayCollection();
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
     * @param mixed $relatedChampions
     */
    public function setRelatedChampions($relatedChampions)
    {
        $this->relatedChampions = $relatedChampions;
    }

    /**
     * @return mixed
     */
    public function getRelatedChampions()
    {
        return $this->relatedChampions;
    }



} 