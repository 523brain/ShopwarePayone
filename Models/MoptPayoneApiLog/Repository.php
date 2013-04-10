<?php

/**
 * $Id: $
 */

namespace Shopware\CustomModels\MoptPayoneApiLog;

use Shopware\Components\Model\ModelRepository;

/**
 * Transaction Log Repository
 */
class Repository extends ModelRepository
{

  const KEY = 'p1_shopware_api';

  /**
   * @return string
   */
  public function getKey()
  {
    return self::KEY;
  }

  public function save($request, $response)
  {
//    var_dump($request);
//    var_dump($response); exit;
    $apiLog = new \Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog();

    //special transaction status handling
    if($response instanceof \Payone_TransactionStatus_Request_Interface)
    {
      $apiLog->setRequest(get_class($response));
    }
    else
    {
      $apiLog->setRequest($request->getRequest());
      //$apiLog->setResponse($response->getResponse());
      $apiLog->setResponse($response->getStatus());
      $apiLog->setLiveMode($request->getMode());
      $apiLog->setMerchantId($request->getMid());
      $apiLog->setPortalId($request->getPortalid());
      $apiLog->setCreationDate(date('Y-m-d\TH:i:sP'));
      $apiLog->setRequestDetails($request->__toString());
      $apiLog->setResponseDetails($response->__toString());
    }

    Shopware()->Models()->persist($apiLog);
    Shopware()->Models()->flush();
  }

  /**
   * @param Payone_Api_Request_Interface $request
   * @param Exception
   * @return boolean
   */
  public function saveException(Payone_Api_Request_Interface $request, Exception $ex)
  {
//    $domainObject = $this->getFactory()->getModelApi();
//    $domainObject->setData($request->toArray());
//    $domainObject->setRawRequest($request->__toString());
//    $domainObject->setStacktrace($ex->getTraceAsString());
//    $domainObject->setResponse(Payone_Core_Model_System_Config_ResponseType::EXCEPTION);
//    $domainObject->save();
  }

  //@TODO: implement query builder

  /**
   * Helper function to create the query builder
   * @return \Doctrine\ORM\QueryBuilder
   */
  public function getApiLogQueryBuilder()
  {
    $builder = $this->getEntityManager()->createQueryBuilder();
//    $builder->select(array('id', 'request', 'response', 'liveMode', 'merchantId', 'portalId', 'creationDate'))
    $builder->select(array('m.id', 'm.request', 'm.response', 'm.liveMode', 'm.merchantId', 'm.portalId', 'm.creationDate', 'm.requestDetails', 'm.responseDetails'))
//    $builder->select('m.*')
            ->from('Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog', 'm');
    return $builder;
  }

}