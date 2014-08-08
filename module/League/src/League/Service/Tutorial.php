<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 8/5/14
 * Time: 4:06 PM
 */

namespace League\Service;


use Application\Service\BaseService;
use Zend\Form\Form;
use League\Entity\Tutorial as TutorialEntity;
use League\Repository\ChampionRepository;
use Doctrine\ORM\EntityRepository;

/**
 * Class Tutorial
 * @package League\Service
 * @method ChampionRepository getChampionRepository($namespace)
 * @method EntityRepository getTutorialRepository($namespace)
 */
class Tutorial extends BaseService
{
    /**
     * @var Form The add tutorial form
     */
    private $addTutorialForm;

    /**
     * @var Form The search tutorial form
     */
    private $tutorialForm;

    /**
     * @var \Youtube\Service\Youtube The youtube service
     */
    private $youtubeService;


    public function findTutorials($data)
    {
        $form = $this->getTutorialForm();
        $form->setData($data);
        if ($form->isValid()) {
            $params = array("champion" => $data['tutorial']['champion'],
                "opponent" => $data['tutorial']['opponent']);
            if ($data['tutorial']['position']) $params["position"] = $data['tutorial']['position'];
            return $this->getTutorialRepository('league')->findBy($params);
        }
        return false;
    }

    public function getTutorials($params, $champion)
    {
        $options = array();
        $championRepository = $this->getChampionRepository('league');
        if (is_array($champion)) $champion = array_shift($champion);
        $champion = $championRepository->findOneBy(array("name" => $champion->getName()));
        $options["champion"] = $champion;
        if (isset($params["opponent"]) && !empty($params['opponent'])) {
            $opponent = $championRepository->find($params['opponent']);
            $options["opponent"] = $opponent->getChampionId();
        }
        if (isset($params["position"]) && !empty($params["position"])) {
            $options["position"] = ucfirst($params["position"]);
        }
        $tutorials = $this->getTutorialRepository('league')->findBy($options);
        if (count($tutorials) <= 0) {
            unset($options["opponent"]);
            $tutorials = $this->getTutorialRepository('league')->findBy($options);
        }
        return $tutorials;
    }

    /**
     * Create and save a new tutorial
     *
     * @param array $data The post data
     * @return bool|TutorialEntity
     */
    public function create($data)
    {
        $form = $this->getAddTutorialForm();
        $entity = new TutorialEntity();
        $form->bind($entity);
        $form->setData($data);
        if ($form->isValid()) {
            $em = $this->getEntityManager();
            parse_str(parse_url($data["tutorial"]["videoId"], PHP_URL_QUERY), $vars);
            $entity->setVideoId($vars['v']);
            try {
                $em->persist($entity);
                $em->flush();
                return $entity;
            } catch (\Exception $e) {
                echo $e->getMessage();
                return false;
            }
        }
        return false;
    }

    /**
     * Saves a tutorial into the database
     *
     * @param array $data
     * @return bool
     */
    public function save($data)
    {
        $entity = new TutorialEntity();
        $em = $this->getEntityManager();
        foreach ($data as $key => $value) {
            if (!empty($value)) {
                if ($key == "champion") {
                    $champion = $this->getChampionRepository("league")->findOneBy(array("name" => $value));
                    $entity->setChampion($champion);
                } else if ($key == "opponent") {
                    $champion = $this->getChampionRepository("league")->findOneBy(array("name" => $value));
                    $entity->setOpponent($champion);
                } else {
                    $entity->{'set' . ucfirst($key)}($value);
                }
            } else {
                return false;
            }
        }
        try {
            $em->persist($entity);
            $em->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get a youtuber's uploaded videos
     *
     * @param $youtuber
     * @param null|string $token
     * @param bool $isPlaylist
     * @return array
     */
    public function getYoutuberUploads($youtuber, $token = null, $isPlaylist = false)
    {
        if ($isPlaylist) {
            $uploadsPlaylist = $this->getYoutubeService()->findPlaylistById($youtuber, 50, $token);
        } else {
            $channel = $this->getYoutubeService()->findChannelByUsername($youtuber);
            if (!$channel)
                $channel = $this->getYoutubeService()->findChannelById($youtuber);
            $uploadsPlaylist = $channel->getUploadsPlaylist(50, $token);
        }
        $tutorialRepository = $this->getTutorialRepository('league');
        $videos = array();
        foreach ($uploadsPlaylist->getVideos() as $video) {
            $tutorial = $tutorialRepository->findOneBy(array("videoId" => $video->getId()));
            if (empty($tutorial))
                $videos[] = $video;
        }
        return array("nextToken" => $uploadsPlaylist->getNextPageToken(),
            "videos" => $videos,
            "previousToken" => $uploadsPlaylist->getPreviousPageToken());
    }

    /**
     * @return Form
     */
    public function getAddTutorialForm()
    {
        if (null === $this->addTutorialForm)
            $this->addTutorialForm = $this->getServiceManager()->get('add_tutorial_form');
        return $this->addTutorialForm;
    }

    /**
     * @return Form
     */
    public function getTutorialForm()
    {
        if (null === $this->tutorialForm)
            $this->tutorialForm = $this->getServiceManager()->get('tutorial_form');
        return $this->tutorialForm;
    }

    /**
     * @return \Youtube\Service\Youtube
     */
    public function getYoutubeService()
    {
        if (null === $this->youtubeService)
            $this->youtubeService = $this->getServiceManager()->get('youtube_service');
        return $this->youtubeService;
    }
} 