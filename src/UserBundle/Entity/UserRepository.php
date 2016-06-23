<?php

namespace UserBundle\Entity;

use AppBundle\Entity\Abstraction\AbstractEntityRepository;

class UserRepository extends AbstractEntityRepository
{
    public function findFully($id)
    {
        $q = $this->createQueryBuilder('u')
                  ->where('u.id = :id')->setParameter('id', $id)
                  ->leftJoin('u.posts', 'posts')->addSelect('posts')
                  ->leftJoin('u.pages', 'pages')->addSelect('pages');

        return $q->getQuery()->getOneOrNullResult();
    }
}
