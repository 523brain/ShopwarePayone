<?php

namespace Shopware\Plugins\MoptPaymentPayone\Subscribers;

use Enlight\Event\SubscriberInterface;


class EMail implements SubscriberInterface
{

    /**
    * di container
    * 
    * @var \Shopware\Components\DependencyInjection\Container
    */
    private $container;
    
    /**
     * inject di container
     * 
     * @param \Shopware\Components\DependencyInjection\Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
    /**
     * return array with all subsribed events
     * 
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            
            //add clearing data to email
            'Shopware_Modules_Order_SendMail_FilterVariables' => 'onSendMailFilterVariablesFilter'
        );
    }
      
    public function onSendMailFilterVariablesFilter(Enlight_Hook_HookArgs $args)
  {
    $variables = $args->getReturn();

    //return if not payone preprepayment
    if (!$this->container->get('MoptPayoneMain')->getPaymentHelper()
            ->isPayonePayInAdvance($variables['additional']['payment']['name']))
    {
      return;
    }

    $session = Shopware()->Session();

    if ($session->moptClearingData)
    {
      $variables['additional']['moptPayoneClearingData'] = $session->moptClearingData;
      $args->setReturn($variables);
    }
  }
}
