<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 1/6/2014
 * Time: 7:49 μμ
 */

namespace Application\Initializer;


interface EntityManagerAwareInterface {
    public function setEntityManager($entityManager);
} 