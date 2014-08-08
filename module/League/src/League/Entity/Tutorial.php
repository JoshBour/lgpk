<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 8/5/14
 * Time: 3:41 PM
 */

namespace League\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Tutorial
 * @package League\Entity
 * @ORM\Entity(repositoryClass="\League\Repository\TutorialRepository")
 * @ORM\Table(name="tutorials")
 */
class Tutorial {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", length=11, name="tutorial_id")
     */
    private $tutorialId;

    /**
     * @ORM\OneToOne(targetEntity="champion")
     * @ORM\JoinColumn(name="champion_id", referencedColumnName="champion_id")
     */
    private $champion;

    /**
     * @ORM\OneToOne(targetEntity="champion")
     * @ORM\JoinColumn(name="opponent_id", referencedColumnName="champion_id")
     */
    private $opponent;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $player;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $position;

    /**
     * @ORM\Column(type="string", length=50, name="video_id")
     */
    private $videoId;

    /**
     * Returns the thumbnail url of the Feed, given the quality.
     *
     * @param string $quality
     * @return string
     */
    public function getThumbnail($quality = "default"){
        $prefix = "http://img.youtube.com/vi/{$this->videoId}";
        switch($quality){
            case "0":
                return $prefix . "/0.jpg";
            case "1":
                return $prefix . "/1.jpg";
            case "2":
                return $prefix . "/2.jpg";
            case "3":
                return $prefix . "3.jpg";
            case "high":
                return $prefix . "/hqdefault.jpg";
            case "medium":
                return $prefix . "/mqdefault.jpg";
            case "standard":
                return $prefix . "/sddefault.jpg";
            default:
                return $prefix . "/default.jpg";
        }
    }

    /**
     * @param mixed $champion
     */
    public function setChampion($champion)
    {
        $this->champion = $champion;
    }

    /**
     * @return mixed
     */
    public function getChampion()
    {
        return $this->champion;
    }

    /**
     * @param mixed $opponent
     */
    public function setOpponent($opponent)
    {
        $this->opponent = $opponent;
    }

    /**
     * @return mixed
     */
    public function getOpponent()
    {
        return $this->opponent;
    }

    /**
     * @param mixed $player
     */
    public function setPlayer($player)
    {
        $this->player = $player;
    }

    /**
     * @return mixed
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $tutorialId
     */
    public function setTutorialId($tutorialId)
    {
        $this->tutorialId = $tutorialId;
    }

    /**
     * @return mixed
     */
    public function getTutorialId()
    {
        return $this->tutorialId;
    }

    /**
     * @param mixed $videoId
     */
    public function setVideoId($videoId)
    {
        $this->videoId = $videoId;
    }

    /**
     * @return mixed
     */
    public function getVideoId()
    {
        return $this->videoId;
    }


} 