<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 19/3/2014
 * Time: 6:59 μμ
 */

namespace League\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Champion
 * @package League\Entity
 * @ORM\Entity(repositoryClass="\League\Repository\ChampionRepository")
 * @ORM\Table(name="champions")
 */
class Champion {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", length=11, name="champion_id")
     */
    private $championId;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", length=5, name="riot_id")
     */
    private $riotId;

    /**
     * @ORM\Column(type="integer", length=3, name="meta_value")
     */
    private $metaValue;

    /**
     * @ORM\ManyToMany(targetEntity="Champion", inversedBy="counteredChampions")
     * @ORM\JoinTable(name="champions_counters",
     *      joinColumns={@ORM\JoinColumn(name="champion_id", referencedColumnName="champion_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="counter_id", referencedColumnName="champion_id")}
     *      )
     */
    private $counters;

    /**
     * @ORM\ManyToMany(targetEntity="Champion", mappedBy="counters")
     */
    private $counteredChampions;

    /**
     * @ORM\ManyToMany(targetEntity="Attribute", inversedBy="champions")
     * @ORM\JoinTable(name="champions_attributes",
     *      joinColumns={@ORM\JoinColumn(name="champion_id", referencedColumnName="champion_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="attribute_name", referencedColumnName="name")}
     *      )
     */
    private $attributes;

    public function __construct(){
        $this->counteredChampions = new ArrayCollection();
        $this->counters = new ArrayCollection();
        $this->attributes = new ArrayCollection();
    }

    public function getThumbnail(){
        return ucwords($this->name);
    }

    /**
     * @param mixed $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param mixed $championId
     */
    public function setChampionId($championId)
    {
        $this->championId = $championId;
    }

    /**
     * @return mixed
     */
    public function getChampionId()
    {
        return $this->championId;
    }

    /**
     * @param mixed $counteredChampions
     */
    public function setCounteredChampions($counteredChampions)
    {
        $this->counteredChampions = $counteredChampions;
    }

    /**
     * @return mixed
     */
    public function getCounteredChampions()
    {
        return $this->counteredChampions;
    }

    /**
     * @param mixed $counters
     */
    public function setCounters($counters)
    {
        $this->counters = $counters;
    }

    /**
     * @return mixed
     */
    public function getCounters()
    {
        return $this->counters;
    }

    /**
     * @param mixed $metaValue
     */
    public function setMetaValue($metaValue)
    {
        $this->metaValue = $metaValue;
    }

    /**
     * @return mixed
     */
    public function getMetaValue()
    {
        return $this->metaValue;
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
     * @param mixed $riotId
     */
    public function setRiotId($riotId)
    {
        $this->riotId = $riotId;
    }

    /**
     * @return mixed
     */
    public function getRiotId()
    {
        return $this->riotId;
    }

}