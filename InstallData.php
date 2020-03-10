<?php

namespace AbsoluteWeb\CustomerAttributes\Setup;

use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Eav\Model\Config;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
    /**
     * @var Config
     */
    private $eavConfig;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        Config $eavConfig
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws AlreadyExistsException
     * @throws LocalizedException
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $setup->startSetup();

        $attributes = [
            ['label' => 'Attribute 1', 'code' => 'attr1'],
            ['label' => 'Attribute 2', 'code' => 'attr2'],
            ['label' => 'Attribute 3', 'code' => 'attr3']
        ];

        $sort = 93;
        foreach ($attributes as $attribute) {
            $eavSetup->addAttribute(CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER, $attribute['code'], [
                'label' => $attribute['label'],
                'required' => false,
                'user_defined' => 1,
                'system' => 0,
                'position' => $sort,
                'input' => 'text'
            ]);

            $eavSetup->addAttributeToSet(
                CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
                CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
                null,
                $attribute['code']);

            $amountId = $this->eavConfig->getAttribute(CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER, $attribute['code']);
            $amountId->setData('used_in_forms', [
                'adminhtml_customer',
                'customer_account_edit'
            ]);
            $amountId->getResource()->save($amountId);

            $sort++;
        }

        $setup->endSetup();
    }
}
