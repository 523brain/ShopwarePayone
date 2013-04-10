<?php

/**
 * $Id: $
 */
class Shopware_Controllers_Backend_MoptPaymentPayone extends Shopware_Controllers_Backend_ExtJs
{

  /**
   * this function is called initially and extends the standard template directory
   * @return void
   */
  public function init()
  {
    $this->View()->addTemplateDir(dirname(__FILE__) . "/Views/");
    parent::init();
  }

  /**
   * index action is called if no other action is triggered
   * @return void
   */
//  public function indexAction()
//  {
//    $this->View()->loadTemplate("backend/mopt_payment_payone/app.js");
//  }

  /**
   * gets the api log list
   * 
   * Outputs the api logs as json list.
   */
  public function apiLogsAction()
  {
    $repository = Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
    );

    $builder = $repository->getApiLogQueryBuilder();
    $query   = $builder->getQuery();

    $query->setHydrationMode(
            \Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY
    );

    $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($query);

    $data = array();
    $data = $paginator->getIterator()->getArrayCopy();
    $this->View()->assign(array('success' => false, 'data'    => $data, 'total'   => $paginator->count()));
  }

  public function paymentAction()
  {
    
  }
  
  public function modelAction()
  {
    $repository = Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
    );

    $builder = $repository->getApiLogQueryBuilder();
    $query   = $builder->getQuery();

    $query->setHydrationMode(
            \Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY
    );

    $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($query);

    $data = array();
    $data = $paginator->getIterator()->getArrayCopy();
    $this->View()->assign(array('success' => true, 'data'    => $data, 'total'   => $paginator->count()));
  }

}