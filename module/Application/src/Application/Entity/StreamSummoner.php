<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 8/14/14
 * Time: 12:43 AM
 */

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Stream
 * @package Application\Entity
 * @ORM\Entity
 * @ORM\Table(name="stream_summoners")
 */
class StreamSummoner {
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Stream", inversedBy="summoners")
     * @ORM\JoinColumn(name="stream_id", referencedColumnName="stream_id")
     */
    private $stream;

    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=50)
     */
    private $summoner;

    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=5)
     */
    private $region;

    /**
     * @param mixed $region
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    /**
     * @return mixed
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param mixed $stream
     */
    public function setStream($stream)
    {
        $this->stream = $stream;
    }

    /**
     * @return mixed
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * @param mixed $summoner
     */
    public function setSummoner($summoner)
    {
        $this->summoner = $summoner;
    }

    /**
     * @return mixed
     */
    public function getSummoner()
    {
        return $this->summoner;
    }


} 