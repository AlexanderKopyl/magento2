<?php

namespace Developer\SMS\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Monolog\Handler\StreamHandler;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{

    public $logger;
    private $_resource;
    private $_pricingHelper;

    public function __construct(Context $cont, DirectoryList $dir_list, ResourceConnection $rsrc, PricingHelper $helper)
    {
        $this->_resource = $rsrc;
        $this->_pricingHelper = $helper;
        parent::__construct($cont);
    }


    public function getStoreConfig($path)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getVariables()
    {
        $variables = ['{ORDER-NUMBER}', '{ORDER-TOTAL}', '{ORDER-STATUS}', '{CARRIER-NAME}', '{PAYMENT-NAME}', '{CUSTOMER-NAME}', '{CUSTOMER-EMAIL}'];
        return $variables;
    }

    public function getIsOrderSentToSendPuls($order_id)
    {
        $connection = $this->_resource->getConnection();
        $table = $this->_resource->getTableName('sales_order');
        $query = "select is_send_puls_send from {$table} where entity_id = " . (int)($order_id);
        return (int)($connection->fetchOne($query));
    }

    public function setIsOrderSentToSendPuls($order_id)
    {
        $connection = $this->_resource->getConnection();
        $table = $this->_resource->getTableName('sales_order');
        $query = "update {$table} set is_send_puls_send=1 where entity_id = " . (int)($order_id);
        $connection->query($query);
    }

    public function getIncrementId($order)
    {
        $incrementId = $order->getOriginalIncrementId();
        if ($incrementId == null || empty($incrementId) || !$incrementId) {
            $incrementId = $order->getIncrementId();
        }
        return $incrementId;
    }

    public function getNewOrderMessage($order)
    {

        $variables = $this->getVariables();
        $values = [
            $this->getIncrementId($order),
            strip_tags($this->_pricingHelper->currency($order->getGrandTotal())),
            $order->getStatus(),
            $order->getShippingDescription(),
            $order->getPayment()->getMethodInstance()->getTitle(),
            $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname(),
            $order->getCustomerEmail()
        ];

        $message = $this->getStoreConfig('sms_alert/alert_message/alert_area');

        return str_replace($variables, $values, $message);
    }

    public function sendSMS($order)
    {
        try {
            $message = $this->getNewOrderMessage($order);
            $action_ivent = $this->getStoreConfig('sms_alert/alert_settings/action');
            $data = array(
                "email" => $order->getCustomerEmail(),
                "phone" => $order->getShippingAddress()->getTelephone(),
                "message" => $message
            );

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $action_ivent);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $response = @curl_exec($curl);


            if (!$response) {
                throw new \Magento\Framework\Exception\LocalizedException(curl_error($curl));
            }

            curl_close($curl);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            throw new \Magento\Framework\Exception\LocalizedException($e->getMessage());
        }
    }
}
