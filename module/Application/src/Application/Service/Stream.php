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
        /**
         * @var \Application\Entity\Stream $stream
         */
        $streams = array();
        foreach ($fetchedStreams as $stream) {
            $isStreamActive = $this->isStreamActive($stream->getStreamId());
            if (isset($isStreamActive["stream"]) && $isStreamActive["stream"]) {
                $activeStream = array();
                $activeStream["stream"] = $stream;
                foreach ($stream->getSummoners() as $summoner) {
                    $activeGame = $this->isInGame($summoner->getSummoner(),$summoner->getRegion());
                    if(isset($activeGame["error"])){
                        continue;
                    }else{
                        $id = $this->getActiveChampionId($activeGame["game"],$summoner->getSummoner());
                        if(!isset(\League\Service\League::$championList[$id])) continue;
                        else $champion = \League\Service\League::$championList[$id];
                        $activeStream["champion"] = $champion;
                        break;
                    }
                }
                $streams[] = $activeStream;
            }
        }
        return $streams;
    }

    private function getActiveChampionId($game,$name){
        foreach($game["playerChampionSelections"] as $selection){
            foreach($selection as $attribute){
                if($attribute["summonerInternalName"] == strtolower($name))
                    return $attribute["championId"];
            }
        }
        return false;
    }

    private function isStreamActive($name)
    {
        $url = "https://api.twitch.tv/kraken/streams/" . $name;

        $cacheFile = ROOT_PATH . '/data/stream-cache/' . md5($url);
        if (file_exists($cacheFile)) {
            $lastMod = new \DateTime();
            $lastMod->setTimestamp(strtotime(filemtime($cacheFile)));
            $now = new \DateTime();
            if ($lastMod->diff($now)->format('%s') <= 300) {
                $array = unserialize(file_get_contents($cacheFile));
                return $array;
            }
        }
        try {
            $response = $this->performRequest($url, true);

            $fh = fopen($cacheFile, 'w+');
            fwrite($fh, serialize($response));
            fclose($fh);
            return $response;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function refreshCache($name){
        $url = "https://api.twitch.tv/kraken/streams/" . $name;
        $cacheFile = ROOT_PATH . '/data/stream-cache/' . md5($url);
        try {
            $response = $this->performRequest($url, true);

            $fh = fopen($cacheFile, 'w+');
            fwrite($fh, serialize($response));
            fclose($fh);
            return $response;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function isInGame($name, $region)
    {
        $name = urlencode($name);
        $url = "https://community-league-of-legends.p.mashape.com/api/v1.0/{$region}/summoner/retrieveInProgressSpectatorGameInfo/{$name}";
        return $this->performRequest($url,true, array("X-Mashape-Key: vNOffyjGxfmshPHW16c8Uzqsd6glp1kkvLEjsn37OWSng6LVtj"));
    }

    private function performRequest($url,$decode = true, $extraHeaders = null)
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
        return $decode ? json_decode($response,true) : $response;
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