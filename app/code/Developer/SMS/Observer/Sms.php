<?php
namespace Developer\SMS\Observer;

use Developer\SMS\Helper\Data;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

class Sms implements ObserverInterface
{
    public $alphaHelper;
    public function __construct(Data $alphaHelper)
    {
        $this->alphaHelper = $alphaHelper;
    }

    public function execute(Observer $observer)
    {
        try {
            $order = $observer->getEvent()->getData('order');
            if ($order && $order->getId() > 0) {
                if ($this->alphaHelper->getIsOrderSentToSendPuls($order->getId()) != 1) {
                    $this->alphaHelper->sendSMS($order);
                    $this->alphaHelper->setIsOrderSentToSendPuls($order->getId());
                }
            }

        } catch (LocalizedException $e) {
            throw new LocalizedException($e->getMessage());
        }
    }
}
