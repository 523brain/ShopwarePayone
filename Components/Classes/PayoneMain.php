<?php

/**
 * $Id: $
 */
class Mopt_PayoneMain
{

  const SCORING_GREEN  = 500;
  const SCORING_YELLOW = 300;
  const SCORING_RED    = 100;
  const TRAFFIC_LIGHT__GREEN  = 1;
  const TRAFFIC_LIGHT__YELLOW = 2;
  const TRAFFIC_LIGHT__RED    = 3;

  /**
   * MoptPayoneMain instance
   * @var MoptPayoneMain 
   */
  static protected $instance = null;

  /**
   * Payone Config
   * @var MoptPayoneConfig
   */
  protected $payoneConfig = array();

  /**
   * Payone ParamBuilder
   * @var MoptPayoneParamBuilder 
   */
  protected $paramBuilder = null;

  /**
   * Payone FeedbackHandler
   * @var MoptPayoneFeedbackHandler 
   */
  protected $feedbackHandler = null;

  /**
   * Payone FormHandler
   * @var MoptPayoneFormHandler 
   */
  protected $formHandler = null;

  /**
   * Payone FeedbackHandler
   * @var MoptPayoneHelper 
   */
  protected $helper = null;

  /**
   * singleton accessor
   * 
   * @return type 
   */
  static public function getInstance()
  {
    if (is_null(self::$instance))
    {
      self::$instance = new Mopt_PayoneMain();
    }
    return self::$instance;
  }

  /**
   * config-getter
   * 
   * @return type 
   */
  public function getPayoneConfig($paymentId = 0, $forceReload = false, $asArray = true)
  {
    if (!empty($this->payoneConfig[$paymentId]) && !$forceReload)
    {
      return $this->payoneConfig[$paymentId];
    }

    $repository = Shopware()->Models()->getRepository('Shopware\CustomModels\MoptPayoneConfig\MoptPayoneConfig');
    $data       = $repository->getConfigByPaymentId($paymentId, $asArray);
    
    if ($data === NULL)
    {
      $data = new Shopware\CustomModels\MoptPayoneConfig\MoptPayoneConfig();
      $data->setPaymentId($paymentId);
    }

    return $this->payoneConfig[$paymentId] = $data;
  }

  /**
   * param builder getter
   * 
   * @return type 
   */
  public function getParamBuilder()
  {
    if (is_null($this->paramBuilder))
    {
      $this->paramBuilder = new Mopt_PayoneParamBuilder($this->payoneConfig, $this->getHelper());
    }
    return $this->paramBuilder;
  }

  /**
   * getter method for feedback handler
   * 
   * @return type 
   */
  public function getFeedbackHandler()
  {
    if (is_null($this->feedbackHandler))
    {
      $this->feedbackHandler = new Mopt_PayoneFeedbackhandler();
    }
    return $this->feedbackHandler;
  }

  /**
   * getter method for feedback handler
   * 
   * @return type 
   */
  public function getFormHandler()
  {
    if (is_null($this->formHandler))
    {
      $this->formHandler = new Mopt_PayoneFormHandler();
    }
    return $this->formHandler;
  }

  /**
   * getter method for feedback handler
   * 
   * @return type 
   */
  public function getHelper()
  {
    if (is_null($this->helper))
    {
      $this->helper = new Mopt_PayoneHelper();
    }
    return $this->helper;
  }

}