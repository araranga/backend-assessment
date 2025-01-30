<?php
namespace Custom\GeoIP\Model;

use Magento\Framework\HTTP\Client\Curl;
use Psr\Log\LoggerInterface;

class GeoIPService
{
    protected $curl;
    protected $logger;

    public function __construct(Curl $curl, LoggerInterface $logger)
    {
        $this->curl = $curl;
        $this->logger = $logger;
    }

    public function getCountryCode()
    {
        
        $ip = $this->getClientIp();
        $this->logger->info("GeoIPService: Client IP is {$ip}");

        $url = "http://ip-api.com/json/" . $ip;
        try {
            $this->curl->get($url);
            $response = json_decode($this->curl->getBody(), true);
            $this->logger->info("GeoIPService: API Response: " . json_encode($response));

            return $response['countryCode'] ?? 'Unknown';
        } catch (\Exception $e) {
            // Log the error in case of failure
            $this->logger->error("GeoIPService: Error retrieving country code for IP {$ip} - " . $e->getMessage());
            return 'Unknown';
        }
    }


    private function getClientIp()
    {
        // Check for Cloudflare's header
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            // Cloudflare sets this header, so we return it as the client IP
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        }

        // Fallback to HTTP_X_FORWARDED_FOR if Cloudflare header is not available
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // The forwarded IP can contain a list of IPs, we need the first one
            $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ipList[0]);
        }

        // Fallback to REMOTE_ADDR if no forwarded headers are available
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

}
