<?php
/**
 * Copyright © Megnor, Inc. All rights reserved.
 */
namespace Fulloop\Products\Block\Adminhtml\Import\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('fulloop_products_import_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Import'));
    }
}
