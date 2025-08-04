<?php
namespace Megnor\HoverImage\Setup;

use Magento\Catalog\Model\Product\Attribute\Frontend\Image as ImageFrontendModel;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /** @var \Magento\Eav\Setup\EavSetupFactory $_eavSetupFactory */
    protected $_eavSetupFactory;

    /**
     * InstallData constructor.
     *
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->_eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'hover_image',
            [
                'type'                    => 'varchar',
                'label'                   => 'Hover Image',
                'input'                   => 'media_image',
                'frontend'                => ImageFrontendModel::class,
                'required'                => false,
                'sort_order'              => 2,
                'global'                  => ScopedAttributeInterface::SCOPE_STORE,
                'group'                   => 'image-management',
                'source'                  => Boolean::class,
                'visible'                 => true,
                'user_defined'            => true,
                'visible_on_front'        => false,
                'used_in_product_listing' => true
            ]
        );

        $setup->endSetup();
    }
}
