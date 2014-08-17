<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 8/14/14
 * Time: 4:52 PM
 */

namespace Admin\Model;


class StreamModel
{
    private $streamId;

    private $name;

    private $champion;

    private $thumbnail;

    private $viewers;

    public function __construct($response){
        $this->streamId = $response["stream"]["channel"]["name"];
        $this->name = $response["streamName"];
        $this->champion = $response["streamChampion"];
        $this->thumbnail = $response["stream"]["preview"]["medium"];
        $this->viewers = $response["stream"]["viewers"];
    }

    public function getEmbedPlayer()
    {
        return '<object type="application/x-shockwave-flash" height="378" width="620" id="live_embed_player_flash" data="http://www.twitch.tv/widgets/live_embed_player.swf?channel=' . $this->streamId . '" bgcolor="#000000">
        <param name="allowFullScreen" value="true" />
        <param name="allowScriptAccess" value="always" />
        <param name="allowNetworking" value="all" />
        <param name="movie" value="http://www.twitch.tv/widgets/live_embed_player.swf" />
        <param name="flashvars" value="hostname=www.twitch.tv&channel=' . $this->streamId . '&auto_play=true&start_volume=25" />
        </object>';
    }

    public function getChat(){
        return '<iframe frameborder="0" scrolling="no" src="http://twitch.tv/' . $this->streamId . '/chat?popout=" height="500" width="350"></iframe>';
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
     * @param mixed $thumbnail
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;
    }

    /**
     * @return mixed
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * @param mixed $viewers
     */
    public function setViewers($viewers)
    {
        $this->viewers = $viewers;
    }

    /**
     * @return mixed
     */
    public function getViewers()
    {
        return $this->viewers;
    }


} 