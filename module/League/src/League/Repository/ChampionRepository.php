<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 8/1/14
 * Time: 4:15 PM
 */

namespace League\Repository;

use Doctrine\ORM\EntityRepository;

class ChampionRepository extends EntityRepository
{
    public function findChampionsByPosition($positions)
    {
        $firstPosition = array_shift($positions);
        $i = 2;
        $qb = $this->createQueryBuilder('c');
        $qb->select('c.name')
            ->add('from', 'League\Entity\Champion c LEFT JOIN c.attributes a')
            ->where($qb->expr()->eq('a.name', '?1'))
            ->setParameter("1", $firstPosition);
        if (is_array($positions) && count($positions) > 1) {
            foreach ($positions as $position){
                $qb->orWhere(($qb->expr()->eq('a.name', '?'.$i)))
                    ->setParameter($i, $position);
                $i++;
            }
        };

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
        foreach($query->getResult() as $value){
            $positions[] = $value["name"];
        }
        return $positions;
    }
} 