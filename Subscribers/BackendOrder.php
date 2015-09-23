<?php

namespace Shopware\Plugins\MoptPaymentPayone\Subscribers;

use Enlight\Event\SubscriberInterface;


class BackendOrder implements SubscriberInterface
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
                // extend backend order-overview
                'Enlight_Controller_Action_PostDispatch_Backend_Order' => 'moptExtendController_Backend_Order',
                //add payone fields to list results
                'Shopware_Controllers_Backend_Order::getList::after' => 'Order__getList__after'
        );
    }
    
    public function moptExtendController_Backend_Order(Enlight_Event_EventArgs $args)
    {
        $view = $args->getSubject()->View();
        $view->extendsTemplate('backend/mopt_payone_order/controller/detail.js');
        $view->extendsTemplate('backend/mopt_payone_order/model/position.js');
        $view->extendsTemplate('backend/mopt_payone_order/view/detail/overview.js');
        $view->extendsTemplate('backend/mopt_payone_order/view/detail/position.js');
    }
    
    /**
    * add attribute data to detail-data
    * @parent fnc head: protected function getList($filter, $sort, $offset, $limit)
    * 
    * @param Enlight_Event_EventArgs $args
    */
    public function Order__getList__after(Enlight_Event_EventArgs $args)
    {
      $return = $args->getReturn();
      $helper = $this->container->get('MoptPayoneMain')->getHelper();

      if (empty($return['success']) || empty($return['data']))
      {
        return;
      }

      foreach ($return['data'] as &$order)
      {
        foreach ($order["details"] as &$orderDetail)
        {
          //get detail attribute
          $detailObj                         = Shopware()->Models()->getRepository('Shopware\Models\Order\Detail')
                  ->find($orderDetail['id']);
          $attribute                         = $helper->getOrCreateAttribute($detailObj);
          $orderDetail['moptPayoneCaptured'] = $attribute->getMoptPayoneCaptured();
          $orderDetail['moptPayoneDebit']    = $attribute->getMoptPayoneDebit();
        }
      }

      $args->setReturn($return);
    }
    
}
