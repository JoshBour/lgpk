<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 15/3/2014
 * Time: 8:14 μμ
 */

namespace League\Model;


class RankedStatsDto
{
    /**
     * @var array
     */
    private $champions;

    /**
     * @var float
     */
    private $modifyDate;

    /**
     * @var float
     */
    private $summonerId;

    public function __construct($response)
    {
        $this->summonerId = $response["summonerId"];
        foreach($response["champions"] as $champion){
            $this->champions[] = new ChampionStatsDto($champion);
        }
        $this->modifyDate = $response["modifyDate"];
    }

    /**
     * @param array $champions
     */
    public function setChampions($champions)
    {
        $this->champions = $champions;
    }

    /**
     * @return array
     */
    public function getChampions()
    {
        return $this->champions;
    }

    /**
     * @param float $modifyDate
     */
    public function setModifyDate($modifyDate)
    {
        $this->modifyDate = $modifyDate;
    }

    /**
     * @return float
     */
    public function getModifyDate()
    {
        return $this->modifyDate;
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