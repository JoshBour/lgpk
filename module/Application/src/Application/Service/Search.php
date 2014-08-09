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
 * @package Admin\Service
 * @method ChampionRepository getChampionRepository($namespace)
 */
class Search extends BaseService
{
    private $leagueService;

    public function getResultMetaInfo($suggestions){
        $metaInfo = array();
        if(isset($suggestions["mainSuggestion"]) && !empty($suggestions["mainSuggestion"])){
            $main = $suggestions["mainSuggestion"];
            if(is_array($main)) $main = array_shift($suggestions["mainSuggestion"]);
            $metaInfo["description"] = <<<EOT
            With {$main->getName()} you have a {$main->getWinRatio()}% win ratio.
                This means that out of the {$main->getTotalGames()} games you
                have won {$main->getGamesWon()}.
                Moreover, you have a KDA of {$main->getKda()} which is {$main->getKdaComment()}.
EOT;
        }
        return $metaInfo;
    }

    public function getSearchResults($summoner, $region, $params)
    {
        $leagueRepository = $this->getChampionRepository("league");
        $suggestionArray = array();
        $found = true;
        // check if there is an opponent
        if (!empty($params["opponent"])) {
            $opponent = $leagueRepository->find($params["opponent"]);
            if (!empty($params["position"])) {
                $champions = $this->getUserChampionArray($summoner, $region, array($params["position"]));
            } else {
                $opponentPositions = $leagueRepository->findChampionPositions($opponent->getName());
                $champions = $this->getUserChampionArray($summoner, $region, $opponentPositions);
            }
            if ($curOpponent = $this->championExists($champions, $opponent->getName())) {
                $index = array_search($curOpponent, $champions);
                unset($champions[$index]);
            }
            if (is_array($champions)) {
                $topChampions = array_slice($champions, 0, 9, true);
                if ($params["hasMana"] || $params["hasCC"]) {
                    $topChampions = $this->filterChampions($topChampions, $params);
                    if (empty($topChampions)) return array("found"=>"","suggestions"=>array());;
                }
                $topFiveChampions = array_slice($topChampions, 0, 5, true);
                foreach ($opponent->getCounters() as $counter) {
                    $name = $counter->getName();
                    if ($champion = $this->championExists($topFiveChampions, $name)) {
                        $suggestionArray["mainSuggestion"]["counter"] = $champion;
                        $index = array_search($champion, $topChampions);
                        unset($topChampions[$index]);
                        break;
                    }
                }
                $mainCount = isset($suggestionArray["mainSuggestion"]) ? count($suggestionArray["mainSuggestion"]) : 0;
                if ($mainCount == 0) {
                    $found = false;
                    $suggestionArray["mainSuggestion"] = array_shift($topChampions);
                }
                foreach($topChampions as $champ){
                    if($champ->getScore() > 10){
                        $suggestionArray["secondarySuggestions"][] = $champ;
                    }
                }
            } else {
                return $champions;
            }
        } else {
            $champions = $this->getUserChampionArray($summoner, $region, array($params["position"]));
            $topChampions = array_slice($champions, 0, 9, true);
            if (is_array($champions)) {
                if ($params["hasMana"] || $params["hasCC"]) {
                    $filteredChampions = $this->filterChampions($champions, $params);
                    if (!empty($filteredChampions)) {
                        $suggestionArray["mainSuggestion"] = array_shift($filteredChampions);
                        foreach(array_slice($filteredChampions,0,4,true) as $champion){
                            if($champion->getScore() > 10){
                                $suggestionArray["secondarySuggestions"][] = $champion;
                            }
                        }
                        return array("found"=>$found,"suggestions"=>$suggestionArray);
                    }else{
                        return array("found"=>"","suggestions"=>array());
                    }
                }
                $suggestionArray["mainSuggestion"] = array_shift($topChampions);
                foreach($topChampions as $champ){
                    if($champ->getScore() > 5){
                        $suggestionArray["secondarySuggestions"][] = $champ;
                    }
                }

            } else {
                return $champions;
            }
        }
        return array("found"=>$found,"suggestions"=>$suggestionArray);
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

    private function filterChampions(&$champions, $params)
    {
        $leagueRepository = $this->getChampionRepository("league");
        $filteredChampions = array();
        foreach ($champions as $champion) {
            if ($params["hasMana"]) {
                if ($leagueRepository->checkChampionAttribute($champion->getName(), "manaless")) {
                    if ($params["hasCC"]) {
                        if(!$leagueRepository->checkChampionAttribute($champion->getName(), "hascc")){
                           continue;
                        }
                    }
                    $filteredChampions[] = $champion;
                }
            } else if ($params["hasCC"] && $leagueRepository->checkChampionAttribute($champion->getName(), "hascc")) {
                $filteredChampions[] = $champion;
            }
        }
        return $filteredChampions;
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