<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 15/3/2014
 * Time: 8:14 μμ
 */

namespace League\Model;


class AggregatedStatsDto
{
    /**
     * @var int
     */
    private $totalSessionsPlayed;

    /**
     * @var int
     */
    private $totalSessionsLost;

    /**
     * @var int
     */
    private $totalSessionsWon;

    /**
     * @var int
     */
    private $totalChampionKills;

    /**
     * @var int
     */
    private $totalDamageDealt;

    /**
     * @var int
     */
    private $totalDamageTaken;

    /**
     * @var int
     */
    private $mostChampionKillsPerSession;

    /**
     * @var int
     */
    private $totalMinionKills;

    /**
     * @var int
     */
    private $totalDoubleKills;

    /**
     * @var int
     */
    private $totalTripleKills;

    /**
     * @var int
     */
    private $totalQuadraKills;

    /**
     * @var int
     */
    private $totalPentaKills;

    /**
     * @var int
     */
    private $totalUnrealKills;

    /**
     * @var int
     */
    private $totalDeathsPerSession;

    /**
     * @var int
     */
    private $totalGoldEarned;

    /**
     * @var int
     */
    private $mostSpellsCast;

    /**
     * @var int
     */
    private $totalTurretsKilled;

    /**
     * @var int
     */
    private $totalPhysicalDamageDealt;

    /**
     * @var int
     */
    private $totalMagicDamageDealt;

    /**
     * @var int
     */
    private $totalFirstBlood;

    /**
     * @var int
     */
    private $totalAssists;

    /**
     * @var int
     */
    private $maxChampionsKilled;

    /**
     * @var int
     */
    private $maxNumDeaths;

    public function __construct($stats)
    {
        // this is a faster way to construct the object
        foreach ($stats as $var => $value)
            if (property_exists($this, $var))
                $this->{'set' . ucfirst($var)}($value);
    }

    /**
     * @param int $maxChampionsKilled
     */
    public function setMaxChampionsKilled($maxChampionsKilled)
    {
        $this->maxChampionsKilled = $maxChampionsKilled;
    }

    /**
     * @return int
     */
    public function getMaxChampionsKilled()
    {
        return $this->maxChampionsKilled;
    }

    /**
     * @param int $maxNumDeaths
     */
    public function setMaxNumDeaths($maxNumDeaths)
    {
        $this->maxNumDeaths = $maxNumDeaths;
    }

    /**
     * @return int
     */
    public function getMaxNumDeaths()
    {
        return $this->maxNumDeaths;
    }

    /**
     * @param int $mostChampionKillsPerSession
     */
    public function setMostChampionKillsPerSession($mostChampionKillsPerSession)
    {
        $this->mostChampionKillsPerSession = $mostChampionKillsPerSession;
    }

    /**
     * @return int
     */
    public function getMostChampionKillsPerSession()
    {
        return $this->mostChampionKillsPerSession;
    }

    /**
     * @param int $mostSpellsCast
     */
    public function setMostSpellsCast($mostSpellsCast)
    {
        $this->mostSpellsCast = $mostSpellsCast;
    }

    /**
     * @return int
     */
    public function getMostSpellsCast()
    {
        return $this->mostSpellsCast;
    }

    /**
     * @param int $totalAssists
     */
    public function setTotalAssists($totalAssists)
    {
        $this->totalAssists = $totalAssists;
    }

    /**
     * @return int
     */
    public function getTotalAssists()
    {
        return $this->totalAssists;
    }

    /**
     * @param int $totalChampionKills
     */
    public function setTotalChampionKills($totalChampionKills)
    {
        $this->totalChampionKills = $totalChampionKills;
    }

    /**
     * @return int
     */
    public function getTotalChampionKills()
    {
        return $this->totalChampionKills;
    }

    /**
     * @param int $totalDamageDealt
     */
    public function setTotalDamageDealt($totalDamageDealt)
    {
        $this->totalDamageDealt = $totalDamageDealt;
    }

    /**
     * @return int
     */
    public function getTotalDamageDealt()
    {
        return $this->totalDamageDealt;
    }

    /**
     * @param int $totalDamageTaken
     */
    public function setTotalDamageTaken($totalDamageTaken)
    {
        $this->totalDamageTaken = $totalDamageTaken;
    }

    /**
     * @return int
     */
    public function getTotalDamageTaken()
    {
        return $this->totalDamageTaken;
    }

    /**
     * @param int $totalDeathsPerSession
     */
    public function setTotalDeathsPerSession($totalDeathsPerSession)
    {
        $this->totalDeathsPerSession = $totalDeathsPerSession;
    }

    /**
     * @return int
     */
    public function getTotalDeathsPerSession()
    {
        return $this->totalDeathsPerSession;
    }

    /**
     * @param int $totalDoubleKills
     */
    public function setTotalDoubleKills($totalDoubleKills)
    {
        $this->totalDoubleKills = $totalDoubleKills;
    }

    /**
     * @return int
     */
    public function getTotalDoubleKills()
    {
        return $this->totalDoubleKills;
    }

    /**
     * @param int $totalFirstBlood
     */
    public function setTotalFirstBlood($totalFirstBlood)
    {
        $this->totalFirstBlood = $totalFirstBlood;
    }

    /**
     * @return int
     */
    public function getTotalFirstBlood()
    {
        return $this->totalFirstBlood;
    }

    /**
     * @param int $totalGoldEarned
     */
    public function setTotalGoldEarned($totalGoldEarned)
    {
        $this->totalGoldEarned = $totalGoldEarned;
    }

    /**
     * @return int
     */
    public function getTotalGoldEarned()
    {
        return $this->totalGoldEarned;
    }

    /**
     * @param int $totalMagicDamageDealt
     */
    public function setTotalMagicDamageDealt($totalMagicDamageDealt)
    {
        $this->totalMagicDamageDealt = $totalMagicDamageDealt;
    }

    /**
     * @return int
     */
    public function getTotalMagicDamageDealt()
    {
        return $this->totalMagicDamageDealt;
    }

    /**
     * @param int $totalMinionKills
     */
    public function setTotalMinionKills($totalMinionKills)
    {
        $this->totalMinionKills = $totalMinionKills;
    }

    /**
     * @return int
     */
    public function getTotalMinionKills()
    {
        return $this->totalMinionKills;
    }

    /**
     * @param int $totalPentaKills
     */
    public function setTotalPentaKills($totalPentaKills)
    {
        $this->totalPentaKills = $totalPentaKills;
    }

    /**
     * @return int
     */
    public function getTotalPentaKills()
    {
        return $this->totalPentaKills;
    }

    /**
     * @param int $totalPhysicalDamageDealt
     */
    public function setTotalPhysicalDamageDealt($totalPhysicalDamageDealt)
    {
        $this->totalPhysicalDamageDealt = $totalPhysicalDamageDealt;
    }

    /**
     * @return int
     */
    public function getTotalPhysicalDamageDealt()
    {
        return $this->totalPhysicalDamageDealt;
    }

    /**
     * @param int $totalQuadraKills
     */
    public function setTotalQuadraKills($totalQuadraKills)
    {
        $this->totalQuadraKills = $totalQuadraKills;
    }

    /**
     * @return int
     */
    public function getTotalQuadraKills()
    {
        return $this->totalQuadraKills;
    }

    /**
     * @param int $totalSessionsLost
     */
    public function setTotalSessionsLost($totalSessionsLost)
    {
        $this->totalSessionsLost = $totalSessionsLost;
    }

    /**
     * @return int
     */
    public function getTotalSessionsLost()
    {
        return $this->totalSessionsLost;
    }

    /**
     * @param int $totalSessionsPlayed
     */
    public function setTotalSessionsPlayed($totalSessionsPlayed)
    {
        $this->totalSessionsPlayed = $totalSessionsPlayed;
    }

    /**
     * @return int
     */
    public function getTotalSessionsPlayed()
    {
        return $this->totalSessionsPlayed;
    }

    /**
     * @param int $totalSessionsWon
     */
    public function setTotalSessionsWon($totalSessionsWon)
    {
        $this->totalSessionsWon = $totalSessionsWon;
    }

    /**
     * @return int
     */
    public function getTotalSessionsWon()
    {
        return $this->totalSessionsWon;
    }

    /**
     * @param int $totalTripleKills
     */
    public function setTotalTripleKills($totalTripleKills)
    {
        $this->totalTripleKills = $totalTripleKills;
    }

    /**
     * @return int
     */
    public function getTotalTripleKills()
    {
        return $this->totalTripleKills;
    }

    /**
     * @param int $totalTurretsKilled
     */
    public function setTotalTurretsKilled($totalTurretsKilled)
    {
        $this->totalTurretsKilled = $totalTurretsKilled;
    }

    /**
     * @return int
     */
    public function getTotalTurretsKilled()
    {
        return $this->totalTurretsKilled;
    }

    /**
     * @param int $totalUnrealKills
     */
    public function setTotalUnrealKills($totalUnrealKills)
    {
        $this->totalUnrealKills = $totalUnrealKills;
    }

    /**
     * @return int
     */
    public function getTotalUnrealKills()
    {
        return $this->totalUnrealKills;
    }


} 