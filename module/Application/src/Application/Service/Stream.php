<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 7/28/14
 * Time: 5:13 PM
 */

namespace Application\Service;

use League\Repository\ChampionRepository;
use Doctrine\ORM\EntityRepository;
use League\Model\ChampionStats;

/**
 * Class Stream
 * @package Admin\Service
 * @method EntityRepository getStreamRepository($namespace)
 */
class Stream extends BaseService
{
    private $leagueService;

    public function getActiveStreams()
    {
        $fetchedStreams = $this->getStreamRepository("application")->findAll();
        $cacheFile = ROOT_PATH . '/data/' . md5("active-streams");
        /**
         * @var \Application\Entity\Stream $stream
         */
        $streams = array();
        if (file_exists($cacheFile) && (filemtime($cacheFile) > (time() - 60 * 10))) {
            return unserialize(file_get_contents($cacheFile));
        } else {
            foreach ($fetchedStreams as $stream) {
                $id = $stream->getStreamId();
                $isStreamActive = $this->isStreamActive($id);
                if (isset($isStreamActive["stream"]) && $isStreamActive["stream"]) {
                    $activeStream = array();
                    $activeStream["stream"]["streamId"] = $id;
                    $activeStream["stream"]["name"] = $stream->getName();
                    foreach ($stream->getSummoners() as $summoner) {
                        $activeGame = $this->isInGame($summoner->getSummoner(), $summoner->getRegion());
                        if (isset($activeGame["error"])) {
                            continue;
                        } else {
                            $id = $this->getActiveChampionId($activeGame["game"], $summoner->getSummoner());
//                            if (!isset(\League\Service\League::$championList[$id])) continue;
//                            else
                            $champion = \League\Service\League::$championList[$id];
                            $activeStream["stream"]["summoner"] = $summoner->getSummoner();
                            $activeStream["stream"]["region"] = $summoner->getRegion();
                            $activeStream["champion"] = $champion;
                            break;
                        }
                    }
                    $streams[] = $activeStream;
                }
            }
            file_put_contents($cacheFile, serialize($streams), LOCK_EX);
        }
        return $streams;
    }

    private function getActiveChampionId($game, $name)
    {
        foreach ($game["playerChampionSelections"] as $selection) {
            foreach ($selection as $attribute) {
                $name = strtolower(preg_replace('/\s+/', '', $name));
                if ($attribute["summonerInternalName"] == $name)
                    return $attribute["championId"];
            }
        }
        return false;
    }

    private function isStreamActive($name)
    {
        $url = "https://api.twitch.tv/kraken/streams/" . $name;
        try {
            return $this->performRequest($url, true);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function refreshCache($name)
    {
        $cacheFile = ROOT_PATH . '/data/' . md5("active-streams");
        $streams = unserialize(file_get_contents($cacheFile));
        $url = "https://api.twitch.tv/kraken/streams/" . $name;
        try {
            $response = $this->performRequest($url, true);

            for($i=0;$i<count($streams);$i++){
                if($streams[$i]["stream"]["streamId"] == $name){
                    if (isset($response["stream"]) && $response["stream"]) {
                        if(isset($streams[$i]["stream"]["summoner"])){
                            $activeGame = $this->isInGame($streams[$i]["stream"]["summoner"],$streams[$i]["stream"]["region"]);
                            if (isset($activeGame["error"])) {
                                unset($streams[$i]["champion"]);
                            }else{
                                $id = $this->getActiveChampionId($activeGame["game"],$streams[$i]["stream"]["summoner"]);
                                if (isset(\League\Service\League::$championList[$id]))
                                    $streams[$i]["champion"] = $id;
                            }
                        }
                    }else{
                        unset($streams[$i]);
                    }
                    break;
                }
            }
            file_put_contents($cacheFile, serialize($streams), LOCK_EX);
            return $response;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function isInGame($name, $region)
    {
        $name = urlencode($name);
        $url = "https://community-league-of-legends.p.mashape.com/api/v1.0/{$region}/summoner/retrieveInProgressSpectatorGameInfo/{$name}";
        return $this->performRequest($url, true, array("X-Mashape-Key: vNOffyjGxfmshPHW16c8Uzqsd6glp1kkvLEjsn37OWSng6LVtj"));
    }

    private function performRequest($url, $decode = true, $extraHeaders = null)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        if ($extraHeaders)
            curl_setopt($curl, CURLOPT_HTTPHEADER, $extraHeaders);
        curl_setopt($curl, CURLOPT_CAINFO, ROOT_PATH . '/data/cacert.pem');
        $response = curl_exec($curl);
        if ($response === false) {
            throw new \Exception(curl_error($curl));
        }
        curl_close($curl);
        return $decode ? json_decode($response, true) : $response;
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