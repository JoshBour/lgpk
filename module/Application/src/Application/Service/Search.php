<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 7/28/14
 * Time: 5:13 PM
 */

namespace Application\Service;

use League\Repository\ChampionRepository;
use League\Model\ChampionStats;

/**
 * Class Search
 * @package Application\Service
 * @method ChampionRepository getChampionRepository($namespace)
 */
class Search extends BaseService
{
    private $leagueService;

    public function getSearchResults($summoner, $region, $opponent, $position)
    {
        $leagueRepository = $this->getChampionRepository("league");
        // first, depending on if a opponent champion is entered, we get a list of suggestions
        $suggestionArray = array();
        if (!empty($opponent)) {
            $opponent = $leagueRepository->find($opponent);
            if (!empty($position)) {
                $champions = $this->getUserChampionArray($summoner, $region, array($position));
            } else {
                $opponentPositions = $leagueRepository->findChampionPositions($opponent->getName());
                $champions = $this->getUserChampionArray($summoner, $region, $opponentPositions);
            }
            if($curOpponent = $this->championExists($champions,$opponent->getName())){
                $index = array_search($curOpponent, $champions);
                unset($champions[$index]);
            }
            $topChampions = array_slice($champions, 0, 9, true);
            if (is_array($champions)) {
                $topThreeChampions = array_slice($topChampions, 0, 2, true);
                foreach ($opponent->getCounters() as $counter) {
                    $name = $counter->getName();
                    if ($champion = $this->championExists($topThreeChampions, $name)) {
                        $suggestionArray["mainSuggestion"]["counter"] = $champion;
                        $index = array_search($champion, $champions);
                        unset($champions[$index]);
                        break;
                    }
                }
                $mainCount = isset($suggestionArray["mainSuggestion"]) ? count($suggestionArray["mainSuggestion"]) : 0;
                if ($mainCount != 0) {
                    $suggestionArray["secondarySuggestions"] = array_slice($champions,0,4,true);
                } else {
                    $suggestionArray["mainSuggestion"] = array_shift($topChampions);
                    $suggestionArray["secondarySuggestions"] = $topChampions;
                }
            } else {
                return $champions;
            }
        } else {
            $champions = $this->getUserChampionArray($summoner, $region, array($position));
            $topChampions = array_slice($champions, 0, 9, true);
            if (is_array($champions)) {
                $suggestionArray["mainSuggestion"] = array_shift($topChampions);
                $suggestionArray["secondarySuggestions"] = $topChampions;
            } else {
                return $champions;
            }
        }
        return $suggestionArray;
    }

    public function getUserChampionStats($name, $region, $championName)
    {
        $id = array_search($championName, \League\Service\League::$championList);
        $leagueService = $this->getLeagueService();
        $summoner = $leagueService->getSummoner($name, $region);
        $stats = $leagueService->getStats($summoner->getId(), $region);
        foreach ($stats->getChampions() as $champion) {
            if ($champion && $champion->getId() == $id) {
                return new ChampionStats($champion);
            }
        }
        return false;
    }

    private function getUserChampionArray($name, $region, $position)
    {
        $leagueService = $this->getLeagueService();
        $vocabulary = $this->getVocabulary();
        try {
            $summoner = $leagueService->getSummoner($name, $region);
            $stats = $leagueService->getStats($summoner->getId(), $region);
        } catch (\Exception $e) {
            return $vocabulary["ERROR_RIOT"];
        }
        if (!$summoner->getId()) {
            return $vocabulary["ERROR_RIOT"];
        }
        $scoreArray = array();
        if (empty($stats)) {
            return $vocabulary["ERROR_NO_RANKED_GAMES"];
        }
        $champions = empty($position) ? array() : $this->getChampionRepository('league')->findChampionsByPosition($position);
        /**
         * @var \League\Model\ChampionStatsDto $champion
         */
        foreach ($stats->getChampions() as $champion) {
            if ($champion && $champion->getId()) {
                if (!empty($champions)) {
                    if (!$this->isChampionInPosition(\League\Service\League::$championList[$champion->getId()], $champions))
                        continue;
                }
                $scoreArray[] = new ChampionStats($champion);
            }
        }
        uasort($scoreArray, function ($a, $b) {
            return ($a->getScore() > $b->getScore()) ? -1 : 1;
        });
        return $scoreArray;
    }

    private function isChampionInPosition($name, &$champions)
    {
        foreach ($champions as $champion)
            if ($champion["name"] == $name) return true;
        return false;

    }

    private function championExists($array, $needle)
    {
        foreach ($array as $value) {
            if (strtolower($value->getName()) == strtolower($needle))
                return $value;
        }
        return false;
    }

    /**
     * @return \League\Service\League
     */
    public function getLeagueService()
    {
        if (null === $this->leagueService)
            $this->leagueService = $this->getServiceManager()->get('league_service');
        return $this->leagueService;
    }
} 