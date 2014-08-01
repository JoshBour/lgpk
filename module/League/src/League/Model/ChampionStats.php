<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 7/31/14
 * Time: 6:23 PM
 */

namespace League\Model;


class ChampionStats {
    private $name;

    private $kills;

    private $deaths;

    private $assists;

    private $gamesWon;

    private $gamesLost;

    private $totalGames;

    public function __construct(ChampionStatsDto $champion){
        $stats = $champion->getStats();
        $this->name =  \League\Service\League::$championList[$champion->getId()];
        $this->kills = $stats->getTotalChampionKills();
        $this->deaths = $stats->getTotalDeathsPerSession();
        $this->assists = $stats->getTotalAssists();
        $this->gamesWon = $stats->getTotalSessionsWon();
        $this->gamesLost = $stats->getTotalSessionsLost();
        $this->totalGames = $stats->getTotalSessionsPlayed();
    }

    public function getWinRatio(){

        return $this->gamesWon == 0 ? 0 : round(($this->gamesWon/$this->totalGames)*100,1,PHP_ROUND_HALF_EVEN);
    }

    public function getScore()
    {
        $ratio = $this->getWinRatio();
        $k = ($this->gamesWon)*($ratio^2);
        if($k == 0) $k = 0.001;
        return round($k,2,PHP_ROUND_HALF_EVEN);
    }

    public function getKda()
    {
        if ($this->assists && $this->deaths && $this->kills) {
            return round(($this->assists + $this->kills) / $this->deaths,1,PHP_ROUND_HALF_EVEN);
        }
        return 0;
    }

    /**
     * @param mixed $assists
     */
    public function setAssists($assists)
    {
        $this->assists = $assists;
    }

    /**
     * @return mixed
     */
    public function getAssists()
    {
        return $this->assists;
    }

    /**
     * @param mixed $deaths
     */
    public function setDeaths($deaths)
    {
        $this->deaths = $deaths;
    }

    /**
     * @return mixed
     */
    public function getDeaths()
    {
        return $this->deaths;
    }

    /**
     * @param mixed $gamesLost
     */
    public function setGamesLost($gamesLost)
    {
        $this->gamesLost = $gamesLost;
    }

    /**
     * @return mixed
     */
    public function getGamesLost()
    {
        return $this->gamesLost;
    }

    /**
     * @param mixed $gamesWon
     */
    public function setGamesWon($gamesWon)
    {
        $this->gamesWon = $gamesWon;
    }

    /**
     * @return mixed
     */
    public function getGamesWon()
    {
        return $this->gamesWon;
    }

    /**
     * @param mixed $kills
     */
    public function setKills($kills)
    {
        $this->kills = $kills;
    }

    /**
     * @return mixed
     */
    public function getKills()
    {
        return $this->kills;
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
     * @param mixed $totalGames
     */
    public function setTotalGames($totalGames)
    {
        $this->totalGames = $totalGames;
    }

    /**
     * @return mixed
     */
    public function getTotalGames()
    {
        return $this->totalGames;
    }


} 