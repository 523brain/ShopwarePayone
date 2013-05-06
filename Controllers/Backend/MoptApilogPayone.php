<?php

/**
 * $Id: $
 */
class Shopware_Controllers_Backend_MoptApilogPayone extends Shopware_Controllers_Backend_ExtJs
{
  /**
   * Sets the ACL-rights for the log-module
   */
//  public function initAcl()
//  {
//    $this->addAclPermission("getLogs", "read", "You're not allowed to see the logs.");
//    $this->addAclPermission("deleteLogs", "delete", "You're not allowed to delete the logs.");
//  }

  /**
   * Disable template engine for all actions
   *
   * @return void
   */
//  public function preDispatch()
//  {
//    if (!in_array($this->Request()->getActionName(), array('index', 'load')))
//    {
//      $this->Front()->Plugins()->Json()->setRenderer(true);
//    }
//  }

  /**
   * This function is called, when the user opens the log-module.
   * It reads the logs from s_core_log
   * Additionally it sets a filterValue
   */
  public function getApilogsAction()
  {
    $start = $this->Request()->get('start');
    $limit = $this->Request()->get('limit');

    //Get the value itself
    if ($this->Request()->get('filter'))
    {
      $filter      = $this->Request()->get('filter');
      $filter      = $filter[count($filter) - 1];
      $filterValue = $filter['value'];
    }


    $builder = Shopware()->Models()->createQueryBuilder();
    $builder->select(
            'log.id as id', 'log.request as request', 'log.response as response', 'log.liveMode as liveMode', 'log.merchantId as merchantId', 'log.portalId as portalId', 'log.creationDate as creationDate', 'log.requestDetails as requestDetails', 'log.responseDetails as responseDetails'
    )->from('Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog', 'log');


    //order data
    $order = (array) $this->Request()->getParam('sort', array());
    if ($order)
    {
      foreach ($order as $ord)
      {
        $builder->addOrderBy('log.' . $ord['property'], $ord['direction']);
      }
    }
    else
    {
      $builder->addOrderBy('log.creationDate', 'DESC');
    }

    if ($filterValue)
    {
      $builder->where('log.merchantId = ?1')->setParameter(1, $filterValue);
    }
//    $builder->addOrderBy($order);


    $builder->setFirstResult($start)->setMaxResults($limit);

    $result = $builder->getQuery()->getArrayResult();

    $result = $this->addArrayRequestResponse($result);

    $total = Shopware()->Models()->getQueryCount($builder->getQuery());

    $this->View()->assign(array('success' => true, 'data'    => $result, 'total'   => $total));
  }

  public function getGridDataAction()
  {
    $type = $this->Request()->get('type');

    $builder = Shopware()->Models()->createQueryBuilder();
    $builder->select('log.id as id', 'log.requestDetails as requestDetails', 'log.responseDetails as responseDetails')
            ->from('Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog', 'log')
            ->where('log.id = ?1')
            ->setParameter(1, $this->Request()->get('id'));

    $result = $builder->getQuery()->getArrayResult();

    $result = $this->addArrayRequestResponse($result);

    $total = Shopware()->Models()->getQueryCount($builder->getQuery());
    $this->View()->assign(array('success' => true, 'data'    => $result[0][$type . 'Array'], 'total'   => $total));
  }

  protected function addArrayRequestResponse($result)
  {
    if (!empty($result))
    {

      foreach ($result as $key => $entry)
      {
        $request  = array();
        $response = array();

        $dataRequest = explode('|', $entry['requestDetails']);

        foreach ($dataRequest as $value)
        {
          $tmp       = explode('=', $value);
          $request[] = array('name'  => $tmp[0], 'value' => $tmp[1]);
        }

        $dataResponse = explode('|', $entry['responseDetails']);
        foreach ($dataResponse as $value)
        {
          $tmp        = explode('=', $value);
          $response[] = array('name'  => $tmp[0], 'value' => $tmp[1]);
        }

        $result[$key]['requestArray']  = $request;
        $result[$key]['responseArray'] = $response;
      }
    }
    return $result;
  }

  public function controllerAction()
  {
    $start = $this->Request()->get('start');
    $limit = $this->Request()->get('limit');

    //order data
    $order = (array) $this->Request()->getParam('sort', array());
    //Get the value itself
    if ($this->Request()->get('filter'))
    {
      $filter      = $this->Request()->get('filter');
      $filter      = $filter[count($filter) - 1];
      $filterValue = $filter['value'];
    }

    $builder = Shopware()->Models()->createQueryBuilder();
    $builder->select(
            'log.id as id', 'log.request as request', 'log.response as response', 'log.liveMode as liveMode', 'log.merchantId as merchantId', 'log.portalId as portalId', 'log.creationDate as creationDate', 'log.requestDetails as requestDetails', 'log.responseDetails as responseDetails'
    )->from('Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog', 'log');

    if ($filterValue)
    {
      $builder->where('log.merchant_id = ?1')->setParameter(1, $filterValue);
    }
    $builder->addOrderBy($order);

    $builder->setFirstResult($start)->setMaxResults($limit);

    $result = $builder->getQuery()->getArrayResult();
    $total  = Shopware()->Models()->getQueryCount($builder->getQuery());


    $this->View()->assign(array('success' => true, 'data'    => $result, 'total'   => $total));
  }

  public function getSearchResultAction()
  {
    $filters = $this->Request()->get('filter');

    $builder = Shopware()->Models()->createQueryBuilder();
    $builder->select(
            'log.id as id', 'log.request as request', 'log.response as response', 'log.liveMode as liveMode', 'log.merchantId as merchantId', 'log.portalId as portalId', 'log.creationDate as creationDate', 'log.requestDetails as requestDetails', 'log.responseDetails as responseDetails'
    )->from('Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog', 'log');

    foreach ($filters as $filter)
    {
      if ($filter['property'] == 'search' && !empty($filter['value']))
      {
        $builder->where($builder->expr()->orx($builder->expr()->like('log.requestDetails', $builder->expr()->literal(
                                        '%' . $filter['value'] . '%')), $builder->expr()->like('log.responseDetails', $builder->expr()->literal(
                                        '%' . $filter['value'] . '%'))
        ));
      }
      elseif ($filter['property'] == 'searchtrans' && !empty($filter['value']))
      {
        $builder->where($builder->expr()->orx($builder->expr()->like('log.responseDetails', $builder->expr()->literal(
                                        '%txid=' . $filter['value'] . '%'))));
      }
    }


    $builder->setMaxResults(20);
    $result = $builder->getQuery()->getArrayResult();
    $total  = Shopware()->Models()->getQueryCount($builder->getQuery());

    $this->View()->assign(array('success' => true, 'data'    => $result, 'total'   => $total));
  }

  /**
   * This function is called when the user wants to delete a log.
   * It only handles the deletion.
   */
  public function deleteLogsAction()
  {
//    try
//    {
//      $params = $this->Request()->getParams();
//      unset($params['module']);
//      unset($params['controller']);
//      unset($params['action']);
//      unset($params['_dc']);
//
//      if ($params[0])
//      {
//        $data = array();
//        foreach ($params as $values)
//        {
//          $logModel = Shopware()->Models()->find('\Shopware\Models\Log\Log', $values['id']);
//
//          Shopware()->Models()->remove($logModel);
//          Shopware()->Models()->flush();
//          $data[] = Shopware()->Models()->toArray($logModel);
//        }
//      }
//      else
//      {
//        $logModel = Shopware()->Models()->find('\Shopware\Models\Log\Log', $params['id']);
//
//        Shopware()->Models()->remove($logModel);
//        Shopware()->Models()->flush();
//      }
//      $this->View()->assign(array('success' => true, 'data'    => $params));
//    }
//    catch (Exception $e)
//    {
//      $this->View()->assign(array('success'  => false, 'errorMsg' => $e->getMessage()));
//    }
    $this->View()->assign(array('success'  => false, 'errorMsg' => 'geht nicht'));
  }

  /**
   * This method is called when a new log is made automatically.
   * It sets the different values and saves the log into s_core_log
   */
  public function createLogAction()
  {
//    try
//    {
//      $params        = $this->Request()->getParams();
//      $params['key'] = utf8_encode(html_entity_decode($params['key']));
//
//      $logModel = new Shopware\Models\Log\Log;
//
//      $userAgent = $_SERVER['HTTP_USER_AGENT'];
//      if (empty($userAgent))
//      {
//        $userAgent = 'Unknown';
//      }
//      $logModel->fromArray($params);
//      $logModel->setDate(new \DateTime("now"));
//      $logModel->setIpAddress(getenv("REMOTE_ADDR"));
//      $logModel->setUserAgent($userAgent);
//
//      Shopware()->Models()->persist($logModel);
//      Shopware()->Models()->flush();
//
//      $data = Shopware()->Models()->toArray($logModel);
//
//      $this->View()->assign(array('success' => true, 'data'    => $data));
//    }
//    catch (Exception $e)
//    {
//      $this->View()->assign(array('success'  => false, 'errorMsg' => $e->getMessage()));
//    }
    $this->View()->assign(array('success'  => false, 'errorMsg' => 'geht nicht'));
  }

}