<?php

/**
 * $Id: $
 */

namespace Shopware\CustomModels\MoptPayoneTransactionForward;

use Shopware\Components\Model\ModelRepository;

/**
 * Transaction Log Repository
 */
class Repository extends ModelRepository
{

  /**
   * Helper function to create the query builder
   * @return \Doctrine\ORM\QueryBuilder
   */
  public function getTransactionForwardQueryBuilder()
  {
    $builder = $this->getEntityManager()->createQueryBuilder();
    $builder->select(array('t.id', 't.status', 't.urls'))
            ->from('Shopware\CustomModels\MoptPayoneTransactionForward\MoptPayoneTransactionForward', 't');
    return $builder;
  }

}