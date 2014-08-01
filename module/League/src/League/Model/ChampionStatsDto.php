<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 15/3/2014
 * Time: 8:14 μμ
 */

namespace League\Model;


class ChampionStatsDto
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var AggregatedStatsDto
     */
    private $stats;

    public function __construct($response)
    {
        $this->id = $response["id"];
        $this->stats = new AggregatedStatsDto($response["stats"]);
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \League\Model\AggregatedStatsDto $stats
     */
    public function setStats($stats)
    {
        $this->stats = $stats;
    }

    /**
     * @return \League\Model\AggregatedStatsDto
     */
    public function getStats()
    {
        return $this->stats;
    }


} 