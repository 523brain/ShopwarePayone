<?php

/**
 * $Id: $
 */
class Shopware_Controllers_Backend_MoptConfigPayone extends Shopware_Controllers_Backend_ExtJs
{

  protected $repository = null;

  /**
   * Disable template engine for all actions
   *
   * @return void
   */
  public function preDispatch()
  {
    if (!in_array($this->Request()->getActionName(), array('index', 'load')))
    {
      $this->Front()->Plugins()->Json()->setRenderer(true);
    }
  }

  /**
   * @return \Shopware\CustomModels\MoptPayoneConfig\Repository
   */
  public function getRepository()
  {
    if ($this->repository === null)
    {
      $this->repository = Shopware()->Models()->getRepository(
              'Shopware\CustomModels\MoptPayoneConfig\MoptPayoneConfig'
      );
    }
    return $this->repository;
  }

  public function getConfigAction()
  {
    $data = Shopware()->Plugins()->Frontend()->MoptPaymentPayone()->Application()->PayoneMain()->getPayoneConfig(0, true);

    $this->View()->assign(array(
        "success"    => true,
        "data"       => $data,
        "totalCount" => count($data))
    );
  }

  /**
   * Controller action which can be called over an ajax request.
   * This function can be used to update an existing favorite.
   */
  public function updateConfigAction()
  {
    $this->View()->assign(
            $this->saveConfig()
    );
  }

  public function saveConfigAction()
  {


    $data = $this->Request()->getParams();
    if (isset($data['type']) && $data['type'] == 'reset' && $data['paymentId'] != 0)
    {
      return $this->deleteConfigAction($data);
    }
    $data = $this->validateFormData($data);

//    $config = Mopt_PayoneMain::getInstance()->getPayoneConfig($data['paymentId'], true, false);
    $config = Shopware()->Plugins()->Frontend()->MoptPaymentPayone()->Application()->PayoneMain()->getInstance()->getPayoneConfig($data['paymentId'], true, false);

    if ($data['paymentId'] == $config->getPaymentId())
    {
      $config->fromArray($data);
    }
    else
    {
      $config = new Shopware\CustomModels\MoptPayoneConfig\MoptPayoneConfig();
      $config->setData($data);
    }

    Shopware()->Models()->persist($config);
    Shopware()->Models()->flush();

    $this->View()->assign(array(
        'success' => true,
        'data'    => 'Erfolgreich gespeichert'
    ));
  }

  protected function deleteConfigAction($data)
  {
    $data = Shopware()->Plugins()->Frontend()->MoptPaymentPayone()->Application()->PayoneMain()->getPayoneConfig(
            $data['paymentId'], true, false
    );

    if ($data->getPaymentId() != 0)
    {
      Shopware()->Models()->remove($data);
      Shopware()->Models()->flush();
      $msg = 'Erfolgreich zurückgesetzt';
    }
    else
    {
      $msg = 'Diese Zahlart besitzt keine spezielle Konfiguration';
    }

    $this->View()->assign(array(
        "success" => true,
        "data"    => $msg
    ));
  }

  protected function validateFormData($data = array())
  {

    $fields = array(
        'consumerscoreAgreementActive',
        'consumerscoreNoteActive',
        'consumerscoreAbtestActive',
        'consumerscoreAgreementActive',
        'liveMode',
        'submitBasket',
        'adresscheckActive',
        'adresscheckLiveMode',
        'consumerscoreLiveMode',
        'consumerscoreActive',
        'checkCc',
    );

    foreach ($fields as $field)
    {
      $data[$field] = ($data[$field] == 'off' || $data[$field] == false || $data[$field] == 'false') ? 0 : 1;
    }

    return $data;
  }

  public function getPaymentsAction()
  {
    $builder = Shopware()->Models()->createQueryBuilder();
    $data    = $builder->select('a.id, a.description')
                    ->from('Shopware\Models\Payment\Payment a')
                    ->where('a.name LIKE \'mopt_payone__%\'')
                    ->getQuery()->getArrayResult();

    array_unshift($data, array('id'          => null, 'description' => 'Alle (global)'));

    $this->View()->assign(array(
        "success"    => true,
        "data"       => $data,
        "totalCount" => count($data))
    );
  }

  /**
   * 
   */
  public function getPaymentConfigAction()
  {
    $data = $this->Request()->getParams();

    foreach ($data['filter'] as $filter)
    {
      if ($filter['property'] === 'payment_id')
      {

        $data = Shopware()->Plugins()->Frontend()->MoptPaymentPayone()->Application()->PayoneMain()->getPayoneConfig($filter['value'], true, true);

        //getPaymentName 
        $builder     = Shopware()->Models()->createQueryBuilder();
        $paymentData = $builder->select('a.name')
                        ->from('Shopware\Models\Payment\Payment a')
                        ->where('a.id = ?1')
                        ->setParameter(1, $filter['value'])
                        ->getQuery()->getOneOrNullResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

        if ($paymentData && $paymentData['name'])
        {
          if (preg_match('#mopt_payone#', $paymentData['name']))
          {
            $data['extra'] = 'p1';
          }
          if ($paymentData['name'] === 'mopt_payone__acc_debitnote')
          {
            $data['extra'] = 'debit';
          }
          if (preg_match('#mopt_payone__cc#', $paymentData['name']))
          {
            $data['extra'] = 'cc';
          }
        }

        $this->View()->assign(array('success' => true, 'data'    => $data));
      }
    }
  }

  public function readPaymentStateAction()
  {
    $builder = Shopware()->Models()->createQueryBuilder();
    $data    = $builder->select('a.id, a.description')
                    ->from('Shopware\Models\Order\Status a')
                    ->where('a.group = \'payment\'')
                    ->getQuery()->getArrayResult();

    $this->View()->assign(array('data'    => $data, 'success' => true));
  }

}