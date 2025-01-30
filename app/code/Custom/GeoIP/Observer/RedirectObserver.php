<?php
namespace Custom\GeoIP\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Controller\ResultFactory;
use Custom\GeoIP\Model\GeoIPService;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\Redirect;

class RedirectObserver implements ObserverInterface
{
    protected $geoIPService;
    protected $resultFactory;
    protected $redirect;
    protected $scopeConfig;

    public function __construct(
        GeoIPService $geoIPService,
        ResultFactory $resultFactory,
        RedirectInterface $redirect,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->geoIPService = $geoIPService;
        $this->resultFactory = $resultFactory;
        $this->redirect = $redirect;
        $this->scopeConfig = $scopeConfig;
    }

    public function execute(Observer $observer)
    {
        // Get country code (hardcoded for testing)
        $countryCode = $this->geoIPService->getCountryCode();

        // Check if the country is not Russia (RU) or China (CN)
        if (in_array($countryCode, ['RU', 'CN'])) {
            // Get the current controller action and response
            $controller = $observer->getControllerAction();
            $response = $controller->getResponse();

            // Get the current request URI
            $requestUri = $controller->getRequest()->getPathInfo();

            // Get the 'no_route' configuration from the system configuration
            $noRoute = $this->scopeConfig->getValue('web/default/no_route', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

            // Check if the current URL is already the no-route page
            if (strpos($requestUri, $noRoute) === false) {
                // Redirect to the configured 'no-route' page if it's not already in the URL
                $this->redirect->redirect($response, $noRoute);
            }

            return $this; // No further action needed
        }

        return null; // No redirection if country is not RU or CN
    }
}