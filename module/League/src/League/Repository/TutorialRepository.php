<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 8/1/14
 * Time: 4:15 PM
 */

namespace League\Repository;

use Doctrine\ORM\EntityRepository;

class TutorialRepository extends EntityRepository
{
    public function getTutorialsByParams($params,$champion)
    {
        if(is_array($champion)) $champion = $champion[0];

        $qb = $this->createQueryBuilder('t');
        $qb->select('t')
            ->add('from', 'League\Entity\Tutorial t')
            ->where($qb->expr()->eq('t.champion', '?1'))
            ->setParameter("1", $champion);
        if (isset($params["opponent"]) && !empty($params["opponent"])) {
            $qb->andWhere($qb->expr()->eq('t.opponent', '?2'))
                ->setParameter("2",$params["opponent"]);
        }
        if (isset($params["position"]) && !empty($params["position"])) {
            $qb->andWhere($qb->expr()->eq('t.position', '?3'))
                ->setParameter("3",$params["position"]);
        }
        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function checkChampionAttribute($name, $attribute)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('a.name')
            ->add('from', 'League\Entity\Champion c LEFT JOIN c.attributes a')
            ->where($qb->expr()->eq('c.name', '?1'))
            ->andWhere($qb->expr()->eq('a.name', '?2'))
            ->setParameters(array('1' => $name, '2' => $attribute));

        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function findChampionPositions($name)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('a.name')
            ->add('from', 'League\Entity\Champion c LEFT JOIN c.attributes a')
            ->where($qb->expr()->eq('c.name', '?1'))
            ->andWhere($qb->expr()->neq('a.name', '?2'))
            ->andWhere($qb->expr()->neq('a.name', '?3'))
            ->setParameters(array(
                "1" => $name,
                "2" => "manaless",
                "3" => "hascc"
            ));

        $query = $qb->getQuery();
        $positions = array();
        foreach ($query->getResult() as $value) {
            $positions[] = $value["name"];
        }
        return $positions;
    }
} 