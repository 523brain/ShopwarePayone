<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GNU General Public License (GPL 3)
 * that is bundled with this package in the file LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Payone to newer
 * versions in the future. If you wish to customize Payone for your
 * needs please refer to http://www.payone.de for more information.
 *
 * @category        Payone
 * @package         Payone_Api
 * @copyright       Copyright (c) 2012 <info@noovias.com> - www.noovias.com
 * @author          Matthias Walter <info@noovias.com>
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 3)
 * @link            http://www.noovias.com
 */

/**
 *
 * @category        Payone
 * @package         Payone_Api
 * @copyright       Copyright (c) 2012 <info@noovias.com> - www.noovias.com
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 3)
 * @link            http://www.noovias.com
 */
class Payone_Api_Factory
{
    /** @var Payone_Api_Config */
    protected $config = null;

    /**
     * @return Payone_Api_Adapter_Interface
     */
    protected function buildHttpClient()
    {
        if ($this->isEnabledCurl()) {
            $adapter = new Payone_Api_Adapter_Http_Curl();
        }
        else {
            $adapter = new Payone_Api_Adapter_Http_Socket();
        }
        $adapter->setUrl('https://api.pay1.de/post-gateway/');
        return $adapter;
    }

    /**
     * @return bool
     */
    protected function isEnabledCurl()
    {
        return extension_loaded('curl');
    }

    /**
     * @param string $key Service Key, e.g. 'payment/refund'
     * @return Payone_Api_Service_Payment_Authorize|Payone_Api_Service_Payment_Debit|Payone_Api_Service_Payment_Preauthorize|Payone_Api_Service_Payment_Refund
     * @throws Exception
     */
    public function buildService($key)
    {
        $methodKey = str_replace(' ', '', ucwords(str_replace('/', ' ', $key)));

        $methodName = 'buildService' . $methodKey;
        if (method_exists($this, $methodName)) {
            return $this->$methodName();
        }
        else {
            throw new Exception('Could not build service with key "' . $key . '"');
        }
    }

    /**
     * @return Payone_Api_Mapper_Currency
     */
    public function buildMapperCurrency()
    {
        $mapper = new Payone_Api_Mapper_Currency();
        return $mapper;
    }

    /**
     * @return Payone_Api_Mapper_Request_Payment_Preauthorization
     */
    public function buildMapperRequestPreauthorize()
    {
        $mapper = new Payone_Api_Mapper_Request_Payment_Preauthorization();
        $mapper->setMapperCurrency($this->buildMapperCurrency());
        return $mapper;
    }

    /**
     * @return Payone_Api_Mapper_Request_Payment_Authorization
     */
    public function buildMapperRequestAuthorize()
    {
        $mapper = new Payone_Api_Mapper_Request_Payment_Authorization();
        $mapper->setMapperCurrency($this->buildMapperCurrency());
        return $mapper;
    }

    /**
     * @return Payone_Api_Mapper_Request_Payment_Capture
     */
    public function buildMapperRequestCapture()
    {
        $mapper = new Payone_Api_Mapper_Request_Payment_Capture();
        $mapper->setMapperCurrency($this->buildMapperCurrency());
        return $mapper;
    }

    /**
     * @return Payone_Api_Mapper_Request_Payment_Debit
     */
    public function buildMapperRequestDebit()
    {
        $mapper = new Payone_Api_Mapper_Request_Payment_Debit();
        $mapper->setMapperCurrency($this->buildMapperCurrency());
        return $mapper;
    }

    /**
     * @return Payone_Api_Mapper_Request_Payment_Refund
     */
    public function buildMapperRequestRefund()
    {
        $mapper = new Payone_Api_Mapper_Request_Payment_Refund();
        $mapper->setMapperCurrency($this->buildMapperCurrency());
        return $mapper;
    }

    /**
     * @return Payone_Api_Mapper_Response_Preauthorization
     */
    protected function buildMapperResponsePreauthorize()
    {
        $mapper = new Payone_Api_Mapper_Response_Preauthorization();
        return $mapper;
    }

    /**
     * @return Payone_Api_Mapper_Response_Authorization
     */
    protected function buildMapperResponseAuthorize()
    {
        $mapper = new Payone_Api_Mapper_Response_Authorization();
        return $mapper;
    }

    /**
     * @return Payone_Api_Mapper_Response_Capture
     */
    protected function buildMapperResponseCapture()
    {
        $mapper = new Payone_Api_Mapper_Response_Capture();
        return $mapper;
    }

    /**
     * @return Payone_Api_Mapper_Response_Debit
     */
    protected function buildMapperResponseDebit()
    {
        $mapper = new Payone_Api_Mapper_Response_Debit();
        return $mapper;
    }

    /**
     * @return Payone_Api_Mapper_Response_Refund
     */
    protected function buildMapperResponseRefund()
    {
        $mapper = new Payone_Api_Mapper_Response_Refund();
        return $mapper;
    }

    /**
     * @return Payone_Api_Mapper_Response_3dsCheck
     */
    protected function buildMapperResponse3dsCheck()
    {
        $mapper = new Payone_Api_Mapper_Response_3dsCheck();
        return $mapper;
    }

    /**
     * @return Payone_Api_Mapper_Response_AddressCheck
     */
    protected function buildMapperResponseAddressCheck()
    {
        $mapper = new Payone_Api_Mapper_Response_AddressCheck();
        return $mapper;
    }


    /**
     * @return Payone_Api_Mapper_Response_BankAccountCheck
     */
    protected function buildMapperResponseBankAccountCheck()
    {
        $mapper = new Payone_Api_Mapper_Response_BankAccountCheck();
        return $mapper;
    }

    /**
     * @return Payone_Api_Mapper_Response_Consumerscore
     */
    protected function buildMapperResponseConsumerscore()
    {
        $mapper = new Payone_Api_Mapper_Response_Consumerscore();
        return $mapper;
    }

    /**
     * @return Payone_Api_Mapper_Response_CreditCardCheck
     */
    protected function buildMapperResponseCreditCardCheck()
    {
        $mapper = new Payone_Api_Mapper_Response_CreditCardCheck();
        return $mapper;
    }

    /**
     * @return Payone_Api_Mapper_Response_GetInvoice
     */
    protected function buildMapperResponseGetInvoice()
    {
        $mapper = new Payone_Api_Mapper_Response_GetInvoice();
        return $mapper;
    }

    /**
     * @return Payone_Api_Service_Payment_Preauthorize
     */
    public function buildServicePaymentPreauthorize()
    {
        $service = new Payone_Api_Service_Payment_Preauthorize();
        $service->setAdapter($this->buildHttpClient());
        $service->setMapperRequest($this->buildMapperRequestPreauthorize());
        $service->setMapperResponse($this->buildMapperResponsePreauthorize());
        $service->setValidator($this->buildValidatorDefault());
        return $service;
    }


    /**
     * @return Payone_Api_Service_Payment_Authorize
     */
    public function buildServicePaymentAuthorize()
    {
        $service = new Payone_Api_Service_Payment_Authorize();
        $service->setAdapter($this->buildHttpClient());
        $service->setMapperRequest($this->buildMapperRequestAuthorize());
        $service->setMapperResponse($this->buildMapperResponseAuthorize());
        $service->setValidator($this->buildValidatorDefault());
        return $service;
    }


    /**
     * @return Payone_Api_Service_Payment_Capture
     */
    public function buildServicePaymentCapture()
    {
        $service = new Payone_Api_Service_Payment_Capture();
        $service->setAdapter($this->buildHttpClient());
        $service->setMapperRequest($this->buildMapperRequestCapture());
        $service->setMapperResponse($this->buildMapperResponseCapture());
        $service->setValidator($this->buildValidatorDefault());
        return $service;

    }


    /**
     * @return Payone_Api_Service_Payment_Debit
     */
    public function buildServicePaymentDebit()
    {
        $service = new Payone_Api_Service_Payment_Debit();
        $service->setAdapter($this->buildHttpClient());
        $service->setMapperRequest($this->buildMapperRequestDebit());
        $service->setMapperResponse($this->buildMapperResponseDebit());
        $service->setValidator($this->buildValidatorDefault());
        return $service;

    }

    /**
     * @return Payone_Api_Service_Payment_Refund
     */
    public function buildServicePaymentRefund()
    {
        $service = new Payone_Api_Service_Payment_Refund();
        $service->setAdapter($this->buildHttpClient());
        $service->setMapperRequest($this->buildMapperRequestRefund());
        $service->setMapperResponse($this->buildMapperResponseRefund());
        $service->setValidator($this->buildValidatorDefault());
        return $service;

    }

    /**
     * @return Payone_Api_Service_Verification_3dsCheck
     */
    public function buildServiceVerification3dscheck()
    {
        $service = new Payone_Api_Service_Verification_3dsCheck();
        $service->setAdapter($this->buildHttpClient());
        $service->setMapperResponse($this->buildMapperResponse3dsCheck());
        $service->setValidator($this->buildValidatorDefault());
        return $service;

    }

    /**
     * @return Payone_Api_Service_Verification_AddressCheck
     */
    public function buildServiceVerificationAddressCheck()
    {
        $service = new Payone_Api_Service_Verification_AddressCheck();
        $service->setAdapter($this->buildHttpClient());
        $service->setMapperResponse($this->buildMapperResponseAddressCheck());
        $service->setValidator($this->buildValidatorDefault());
        return $service;

    }

    /**
     * @return Payone_Api_Service_Verification_BankAccountCheck
     */
    public function buildServiceVerificationBankAccountCheck()
    {
        $service = new Payone_Api_Service_Verification_BankAccountCheck();
        $service->setAdapter($this->buildHttpClient());
        $service->setMapperResponse($this->buildMapperResponseBankAccountCheck());
        $service->setValidator($this->buildValidatorDefault());
        return $service;

    }

    /**
     * @return Payone_Api_Service_Verification_Consumerscore
     */
    public function buildServiceVerificationConsumerscore()
    {
        $service = new Payone_Api_Service_Verification_Consumerscore();
        $service->setAdapter($this->buildHttpClient());
        $service->setMapperResponse($this->buildMapperResponseConsumerscore());
        $service->setValidator($this->buildValidatorDefault());
        return $service;

    }

    /**
     * @return Payone_Api_Service_Verification_CreditCardCheck
     */
    public function buildServiceVerificationCreditCardCheck()
    {
        $service = new Payone_Api_Service_Verification_CreditCardCheck();
        $service->setAdapter($this->buildHttpClient());
        $service->setMapperResponse($this->buildMapperResponseCreditCardCheck());
        $service->setValidator($this->buildValidatorDefault());
        return $service;

    }

    /**
     * @return Payone_Api_Service_Management_GetInvoice
     */
    public function buildServiceManagementGetInvoice()
    {
        $service = new Payone_Api_Service_Management_GetInvoice();
        $service->setAdapter($this->buildHttpClient());
        $service->setMapperResponse($this->buildMapperResponseGetInvoice());
        $service->setValidator($this->buildValidatorDefault());
        return $service;

    }

    /**
     * @return Payone_Api_Service_ProtocolRequest
     */
    public function buildServiceProtocolRequest()
    {
        $servicePR = new Payone_Api_Service_ProtocolRequest();

        return $servicePR;
    }

    /**
     * @return Payone_Api_Validator_DefaultParameters
     */
    public function buildValidatorDefault()
    {
        $validator = new Payone_Api_Validator_DefaultParameters();

        return $validator;
    }
}
