<?php
namespace Custom\GeoIP\Block;

use Magento\Framework\View\Element\Template;

class GeoIPBlock extends Template
{
    protected $geoIPService;

    public function __construct(
        Template\Context $context,
        \Custom\GeoIP\Model\GeoIPService $geoIPService,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->geoIPService = $geoIPService;
    }

    public function getCountryCode()
    {
        return $this->geoIPService->getCountryCode();
    }

    public function getStaticBlockIdentifier()
    {
        return ($this->getCountryCode() === 'US') ? 'us_block_data' : 'global_block_data';
    }
}