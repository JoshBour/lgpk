<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 8/14/14
 * Time: 12:43 AM
 */

namespace Application\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Stream
 * @package Application\Entity
 * @ORM\Entity
 * @ORM\Table(name="streams")
 */
class Stream {
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=50, name="stream_id")
     */
    private $streamId;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="StreamSummoner", mappedBy="stream")
     */
    private $summoners;

    public function __construct(){
        $this->summoners = new ArrayCollection();
    }

    public function getThumbnail(){
        return "http://static-cdn.jtvnw.net/previews-ttv/live_user_{$this->streamId}-320x200.jpg";
    }

    public function getStreamPlayer(){
        return '<object type="application/x-shockwave-flash" height="425" width="697" id="live_embed_player_flash" data="http://www.twitch.tv/widgets/live_embed_player.swf?channel=' . $this->streamId . '" bgcolor="#000000">
        <param name="allowFullScreen" value="true" />
        <param name="allowScriptAccess" value="always" />
        <param name="allowNetworking" value="all" />
        <param name="movie" value="http://www.twitch.tv/widgets/live_embed_player.swf" />
        <param name="flashvars" value="hostname=www.twitch.tv&channel='.$this->streamId . '&auto_play=true&start_volume=25" />
        </object>';
    }

    public function getStreamChat(){
        return '<iframe frameborder="0" scrolling="no" src="http://twitch.tv/' . $this->streamId . '/chat?popout=" height="425" width="298"></iframe>';
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
     * @param mixed $streamId
     */
    public function setStreamId($streamId)
    {
        $this->streamId = $streamId;
    }

    /**
     * @return mixed
     */
    public function getStreamId()
    {
        return $this->streamId;
    }

    /**
     * @param mixed $summoners
     */
    public function setSummoners($summoners)
    {
        $this->summoners = $summoners;
    }

    /**
     * @return mixed
     */
    public function getSummoners()
    {
        return $this->summoners;
    }


} 