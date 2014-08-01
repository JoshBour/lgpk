<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 12/3/2014
 * Time: 6:51 μμ
 */

namespace League\Service;

use League\Model\RankedStatsDto;
use League\Model\SummonerDto;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class League implements ServiceManagerAwareInterface
{
    const API_URL_1_1 = 'https://{region}.api.pvp.net/api/lol/{region}/v1.1/';
    const API_URL_1_2 = 'https://{region}.api.pvp.net/api/lol/{region}/v1.2/';
    const API_URL_1_3 = 'https://{region}.api.pvp.net/api/lol/{region}/v1.3/';
    const API_URL_1_4 = 'https://{region}.api.pvp.net/api/lol/{region}/v1.4/';
    const API_URL_2_1 = 'https://{region}.api.pvp.net/api/lol/{region}/v2.2/';
    const RATE_LIMIT_MINUTES = 500;
    const RATE_LIMIT_SECONDS = 10;
    const CACHE_LIFETIME_MINUTES = 60;
    const CACHE_ENABLED = true;
    const ERROR_NOT_SUPPORTED_REGION = "The region you selected is not supported.";

    public static $supportedRegions = array('br', 'eune', 'euw','kr', 'lan', 'las', 'na', 'oce', 'ru', 'tr');

    private $apiKey;

    /**
     * @var ServiceManager
     */
    private $serviceManager;

    private $cacheService;

    public static $championList = array(
        '266' => 'Aatrox', '103' => 'Ahri', '84' => 'Akali', '12' => 'Alistar',
        '32' => 'Amumu', '34' => 'Anivia', '1' => 'Annie', '22' => 'Ashe',
        '53' => 'Blitzcrank', '63' => 'Brand', '51' => 'Caitlyn',
        '69' => 'Cassiopeia', '31' => 'Chogath', '42' => 'Corki',
        '122' => 'Darius', '131' => 'Diana', '119' => 'Draven',
        '36' => 'DrMundo', '60' => 'Elise', '28' => 'Evelynn',
        '81' => 'Ezreal', '9' => 'Fiddlesticks', '114' => 'Fiora',
        '105' => 'Fizz', '3' => 'Galio', '41' => 'Gangplank',
        '86' => 'Garen', '79' => 'Gragas', '104' => 'Graves',
        '120' => 'Hecarim', '74' => 'Heimerdinger', '39' => 'Irelia',
        '40' => 'Janna', '59' => 'JarvanIV', '24' => 'Jax', '126' => 'Jayce',
        '222' => 'Jinx', '43' => 'Karma', '30' => 'Karthus', '38' => 'Kassadin',
        '55' => 'Katarina', '10' => 'Kayle', '85' => 'Kennen', '121' => 'KhaZix',
        '96' => 'KogMaw', '7' => 'LeBlanc', '64' => 'LeeSin', '89' => 'Leona',
        '127' => 'Lissandra', '236' => 'Lucian', '117' => 'Lulu', '99' => 'Lux',
        '54' => 'Malphite', '90' => 'Malzahar', '57' => 'Maokai', '11' => 'MasterYi',
        '21' => 'MissFortune', '62' => 'Wukong', '82' => 'Mordekaiser', '25' => 'Morgana',
        '267' => 'Nami', '75' => 'Nasus', '111' => 'Nautilus', '76' => 'Nidalee', '56' => 'Nocturne',
        '20' => 'Nunu', '2' => 'Olaf', '61' => 'Orianna', '80' => 'Pantheon', '78' => 'Poppy',
        '133' => 'Quinn', '33' => 'Rammus', '58' => 'Renekton', '107' => 'Rengar', '92' => 'Riven',
        '68' => 'Rumble', '13' => 'Ryze', '113' => 'Sejuani', '35' => 'Shaco', '98' => 'Shen',
        '102' => 'Shyvana', '27' => 'Singed', '14' => 'Sion', '15' => 'Sivir', '72' => 'Skarner',
        '37' => 'Sona', '16' => 'Soraka', '50' => 'Swain', '134' => 'Syndra', '91' => 'Talon',
        '44' => 'Taric', '17' => 'Teemo', '412' => 'Thresh', '18' => 'Tristana', '48' => 'Trundle',
        '23' => 'Tryndamere', '4' => 'TwistedFate', '29' => 'Twitch', '77' => 'Udyr', '6' => 'Urgot',
        '110' => 'Varus', '67' => 'Vayne', '45' => 'Veigar', '161' => 'Velkoz', '254' => 'Vi', '112' => 'Viktor',
        '8' => 'Vladimir', '106' => 'Volibear', '19' => 'Warwick', '101' => 'Xerath', '5' => 'XinZhao', '157' => 'Yasuo',
        '83' => 'Yorick', '154' => 'Zac', '238' => 'Zed', '115' => 'Ziggs', '26' => 'Zilean', '143' => 'Zyra', "201" => 'Braum'
    );

    /**
     * @param \League\Entity\Summoner $summoner
     * @return array
     */
    public function getLatestChampions($summoner)
    {
        // we get the latest champions that the player needs to improve
        $games = $this->getGames($summoner->getLolId(), $summoner->getRegion());
        $champions = array();
        if (!empty($games)) {
            foreach ($games as $game) {
                $champions[] = $game->getChampion();
            }
        }
        return array_unique($champions);
    }

    /**
     * @param $summonerId
     * @param $region
     * @param string $mode
     * @param int $season
     * @return RankedStatsDto|null
     * @throws \Exception
     */
    public function getStats($summonerId,$region,$mode = "ranked",$season = 4){
        $region = strtolower($region);
        if (!in_array($region, self::$supportedRegions)) throw new \Exception(self::ERROR_NOT_SUPPORTED_REGION);
        $call = 'stats/by-summoner/' . $summonerId . '/' . $mode;

        //add API URL to the call
        $call = self::API_URL_1_3 . $call;
        $response = json_decode($this->performRequest($call, $region,array("season=SEASON" . $season)), true);
        return $response ? new RankedStatsDto($response) : null;
    }

    public function getChampions($region)
    {
        if (!in_array($region, self::$supportedRegions)) throw new \Exception(self::ERROR_NOT_SUPPORTED_REGION);
        $call = 'champion';

        //add API URL to the call
        $call = self::API_URL_1_1 . $call;
        $response = json_decode($this->performRequest($call, $region), true);
        return $response;
    }

    public function getGames($id, $region)
    {
        if (!in_array($region, self::$supportedRegions)) throw new \Exception(self::ERROR_NOT_SUPPORTED_REGION);
        $call = 'game/by-summoner/' . $id . '/recent';

        //add API URL to the call
        $call = self::API_URL_1_3 . $call;

        $response = json_decode($this->performRequest($call, $region), true);
        $gameDtoList = array();
        if (!empty($response['games'])) {
            foreach ($response['games'] as $game) {
                $gameDtoList[] = new \League\Model\GameDto($game);
            }
        }
        return $gameDtoList;
    }

    public function getSummoner($name, $region)
    {
        $region = strtolower($region);
        if (!in_array($region, self::$supportedRegions)) throw new \Exception(self::ERROR_NOT_SUPPORTED_REGION);
        //sanitize name a bit - this will break weird characters
      #  $name = preg_replace("/[^a-zA-Z0-9 ]+/", "", $name);
        $name = str_replace(' ','',$name);
        $call = 'summoner/by-name/' . $name;

        //add API URL to the call
        $call = self::API_URL_1_4 . $call;
        $response = json_decode($this->performRequest($call, $region), true);
        return new SummonerDto($response[strtolower($name)]);
    }

    private function performRequest($call, $region, $extraOptions = null)
    {

        //probably should put rate limiting stuff here


        //format the full URL
        $url = $this->format_url($call, $region, $extraOptions);
        //caching
        if (self::CACHE_ENABLED) {
            $cacheFile = ROOT_PATH . '/data/cache/' . md5($url);

            if (file_exists($cacheFile)) {
                $fh = fopen($cacheFile, 'r');
                $cacheTime = trim(fgets($fh));

                // if data was cached recently, return cached data
                if ($cacheTime > strtotime('-' . self::CACHE_LIFETIME_MINUTES . ' minutes')) {
                    return fread($fh, filesize($cacheFile));
                }

                // else delete cache file
                fclose($fh);
                unlink($cacheFile);
            }
        }

        //call the API and return the result
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');

        $response = curl_exec($curl);
        if ($response === false) {
            throw new \Exception(curl_error($curl));
        }
        curl_close($curl);

        if (self::CACHE_ENABLED) {
            //create cache file
            $fh = fopen($cacheFile, 'w');
            fwrite($fh, time() . "\n");
            fwrite($fh, $response);
            fclose($fh);
        }

        return $response;

    }

    //creates a full URL you can query on the API
    private function format_url($call, $region,$extraOptions = null)
    {
        $url = str_replace('{region}', $region, $call);
        if($extraOptions){
            $url = $url . '?' . join('&',$extraOptions) . '&api_key=' . $this->getApiKey();
        }else{
            $url = $url . '?api_key=' . $this->getApiKey();
        }
        return $url;
    }

    public function getCacheService()
    {
        if (null === $this->cacheService)
            $this->cacheService = $this->getServiceManager()->get('cache_service');
        return $this->cacheService;
    }

    public function getApiKey()
    {
        if (null === $this->apiKey)
            $this->apiKey = $this->getServiceManager()->get('config')['api']['key'];
        return $this->apiKey;
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param ServiceManager $serviceManager
     * @return Youtube
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }
} 