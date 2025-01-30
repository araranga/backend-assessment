<?php
namespace Custom\GeoIP\Setup;

use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\ResourceModel\Block as BlockResource;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Store\Model\StoreManagerInterface;

class InstallData implements InstallDataInterface
{
    protected $blockFactory;
    protected $blockResource;
    protected $storeManager;

    public function __construct(
        BlockFactory $blockFactory,
        BlockResource $blockResource,
        StoreManagerInterface $storeManager
    ) {
        $this->blockFactory = $blockFactory;
        $this->blockResource = $blockResource;
        $this->storeManager = $storeManager;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $blocks = [
            ['identifier' => 'us_block_data', 'title' => 'US Block', 'content' => '<p>Welcome, US visitors!</p>'],
            ['identifier' => 'global_block_data', 'title' => 'Global Block', 'content' => '<p>Welcome, international visitors!</p>']
        ];

        foreach ($blocks as $data) {
            $block = $this->blockFactory->create();
            $block->setData($data);
            $this->blockResource->save($block);

            // Link the block to all store views (store_id = 0 for all stores)
            $stores = $this->storeManager->getStores(false, true); // Get all store objects
            foreach ($stores as $store) {
                $block->setStoreId($store->getId()); // Use the store ID (getId), not the store object
                $this->blockResource->save($block);
            }
        }

        $setup->endSetup();
    }
}
