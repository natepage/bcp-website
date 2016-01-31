<?php

namespace AppBundle\Entity\Abstraction;

use Doctrine\ORM\EntityRepository;

class AbstractEntityRepository extends EntityRepository
{
    protected function getAlias()
    {
        $fullNameArray = explode('\\', $this->_entityName);
        return strtolower(substr($fullNameArray[count($fullNameArray) - 1], 0, 1));
    }

    /**
     * Get the findAll() query.
     *
     * @return \Doctrine\ORM\Query
     */
    public function getFindAllQuery()
    {
        return $this->createQueryBuilder($this->getAlias())->getQuery();
    }

    public function getFindSearchQuery($search)
    {
        $searchFields = call_user_func($this->_entityName . '::getSearchFields');
        $qb = $this->createQueryBuilder($this->getAlias());

        foreach($searchFields as $field => $type){
            switch($type){
                case 'text':
                    $qb->orWhere($field . ' LIKE \'' . $search . '\'');
                    break;
            }
        }

        return $qb->getQuery();
    }

    /**
     * Get defaults pagination field and direction
     *
     * @return mixed
     */
    public function getDefaultPaginationField()
    {
        return call_user_func($this->_entityName . '::getDefaultPaginationField');
    }
}
