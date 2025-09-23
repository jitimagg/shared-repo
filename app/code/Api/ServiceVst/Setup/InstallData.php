<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Api\ServiceVst\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Config;

class InstallData implements InstallDataInterface
{

    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
		$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
		$eavSetup->addAttribute(
			\Magento\Customer\Model\Customer::ENTITY,
			'enable_sms_notification', [
				'type'          => 'int',
				'label'         => 'Enable SMS Notification',
				'input'         => 'select',
				'required'      => false,
				'visible'       => true,
				'user_defined'  => true,
				'position'      => 30,
				'system'        => 0,
				'default'       => 1,
				'source'        => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
        ]
		);

		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$eavConfig = $objectManager->get(\Magento\Eav\Model\Config::class);
		$sampleAttribute = $eavConfig->getAttribute(Customer::ENTITY, 'enable_sms_notification');

		// more used_in_forms ['adminhtml_checkout','adminhtml_customer','adminhtml_customer_address','customer_account_edit','customer_address_edit','customer_register_address']
		$sampleAttribute->setData(
			'used_in_forms',
			['adminhtml_customer']

		);
		$sampleAttribute->save();
	}
}
