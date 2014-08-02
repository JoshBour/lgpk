<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 8/2/14
 * Time: 4:54 PM
 */

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;

class ReferralRepository extends EntityRepository{
    public function findVisitorByIp($name, $ip)
    {
        $qb = $this->createQueryBuilder('r');
        $qb->select('r')
            ->add('from', 'Application\Entity\Referral r LEFT JOIN r.visitors v')
            ->where($qb->expr()->eq('r.name', '?1'))
            ->andWhere($qb->expr()->eq('v.ip', '?2'))
            ->setParameters(array('1' => $name, '2' => $ip));

        $query = $qb->getQuery();
        return $query->getResult();
    }
} 