<?php

/**
 * $Id: $
 */

namespace Shopware\CustomModels\MoptPayoneTransactionLog;

use Shopware\Components\Model\ModelRepository;

/**
 * Transaction Log Repository
 */
class Repository extends ModelRepository
{

    const KEY = 'p1_shopware_transaction';

  /**
   * @return string
   */
  public function getKey()
  {
    return self::KEY;
  }

  public function save($request, $response)
  {
    $transactionLog = new \Shopware\CustomModels\MoptPayoneTransactionLog\MoptPayoneTransactionLog();

    $transactionLog->setStatus($request->getTxaction());
    $transactionLog->setLiveMode($request->getMode());
    $transactionLog->setPortalId($request->getPortalid());
    $transactionLog->setCreationDate(date('Y-m-d\TH:i:sP'));
    $transactionLog->setUpdateDate(date('Y-m-d\TH:i:sP'));
    $transactionLog->setTransactionDate(date('Y-m-d\TH:i:sP', $request->getTxtime()));
    
    $transactionLog->setTransactionId($request->getTxid());
    $transactionLog->setOrderNr($request->getReference());
    $transactionLog->setSequenceNr($request->getSequencenumber());
    $transactionLog->setPaymentId(Shopware()->Config()->mopt_payone__paymentId);
    $transactionLog->setClaim($request->getReceivable());
    $transactionLog->setBalance($request->getBalance());
    $transactionLog->setDetails($this->buildParamDetails($response));

    Shopware()->Models()->persist($transactionLog);
    Shopware()->Models()->flush();
  }
  
  protected function buildParamDetails($response)
  {
    $details = array_merge($_POST, array('response_state' => $response->getStatus()));
    ksort($details);
    return $details;
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
  
  /**
   * Returns an instance of the \Doctrine\ORM\Query object which selects a list of all mails.
   * @param $transactionId
   * @return \Doctrine\ORM\Query
   */
  public function getTransactionQuery($transactionId)
  {
    $builder = $this->getTransactionQueryBuilder($transactionId);
    return $builder->getQuery();
  }

  /**
   * Helper function to create the query builder for the "getTransactionQuery" function.
   * @param $transactionId
   * @return \Doctrine\ORM\QueryBuilder
   */
  public function getTransactionQueryBuilder($transactionId)
  {
    $builder = $this->getEntityManager()->createQueryBuilder();
    $builder->select(array('id', 'transactionId'))
            ->from('Shopware\CustomModels\MoptPayoneTransactionLog\MoptPayoneTransactionLog', 'MoptPayoneTransactionLog')
            ->where('MoptPayoneTransactionLog.transactionId = ?1')
            ->setParameter(1, $transactionId);
    return $builder;
  }

}