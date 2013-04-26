<?php

/**
 * $Id: $
 */
class Shopware_Controllers_Backend_MoptPayoneTransactionForward extends Shopware_Controllers_Backend_ExtJs
{
  public function getTransactionForwardAction()
  {
//    $builder = Shopware()->Models()->createQueryBuilder();
//    $builder->select('log')
//            ->from('Shopware\CustomModels\MoptPayoneTransactionForward\MoptPayoneTransactionForward');
//
//    $order = (array) $this->Request()->getParam('sort', array());
//
//    if ($order)
//    {
//      foreach ($order as $ord)
//      {
//        $builder->addOrderBy('log.' . $ord['property'], $ord['direction']);
//      }
//    }
//    else
//    {
//      $builder->addOrderBy('log.creationDate', 'DESC');
//    }
//
//    $builder->addOrderBy('log.creationDate', 'DESC');
//
//    $builder->setFirstResult($start)->setMaxResults($limit);
//
//    $result = $builder->getQuery()->getArrayResult();
//    $total  = Shopware()->Models()->getQueryCount($builder->getQuery());
//
//    $this->View()->assign(array('success' => true, 'data'    => $result, 'total'   => $total));
    
    $builder = Shopware()->Models()->createQueryBuilder();
    $data    = $builder->select('t.id, t.status, t.urls')
                    ->from('Shopware\CustomModels\MoptPayoneTransactionForward\MoptPayoneTransactionForward t')
                    ->getQuery()->getArrayResult();

//    array_unshift($data, array('id'          => null, 'description' => 'Alle (global)'));

    $this->View()->assign(array(
        "success"    => true,
        "data"       => $data,
        "totalCount" => count($data))
    );
  }
}