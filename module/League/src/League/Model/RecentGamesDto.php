<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 15/3/2014
 * Time: 8:13 μμ
 */

namespace League\Model;

class RecentGamesDto
{

    /**
     * @var array
     */
    private $games;

    /**
     * @var double
     */
    private $summonerId;

    /**
     * @param array $response
     */
    public function __construct($response)
    {
        $this->summonerId = $response['summonerId'];
        if (!empty($response['games'])) {
            foreach ($response['games'] as $game) {
                $this->games[] = new GameDto($game);
            }
        }
    }

    /**
     * @param array $games
     */
    public function setGames($games)
    {
        $this->games = $games;
    }

    /**
     * @return array
     */
    public function getGames()
    {
        return $this->games;
    }

    /**
     * @param float $summonerId
     */
    public function setSummonerId($summonerId)
    {
        $this->summonerId = $summonerId;
    }

    /**
     * @return float
     */
    public function getSummonerId()
    {
        return $this->summonerId;
    }



}