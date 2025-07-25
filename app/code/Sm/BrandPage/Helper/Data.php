<?php
namespace Sm\BrandPage\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Eav\Model\Config as EavConfig;

class Data extends AbstractHelper
{
    protected $eavConfig;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        EavConfig $eavConfig
    ) {
        $this->eavConfig = $eavConfig;
        parent::__construct($context);
    }

    /**
     * แปลงชื่อแบรนด์ (label) → option_id
     */
    public function getOptionIdByLabel($attributeCode, $label)
    {
        // try {
        //     $attribute = $this->eavConfig->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $attributeCode);
        //     $options = $attribute->getSource()->getAllOptions(false);

        //     foreach ($options as $option) {
        //         if (strcasecmp($option['label'], $label) === 0) {
        //             return $option['value']; // ✅ option_id
        //         }
        //     }
        // } catch (\Exception $e) {
        //     return null;
        // }

        // return null; // ❌ 
        $attribute = $this->eavConfig->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $attributeCode);
        foreach ($attribute->getOptions() as $option) {
            if (strcasecmp($option->getLabel(), $label) === 0) {
                return $option->getValue();
            }
        }
        return null;
    }
}
