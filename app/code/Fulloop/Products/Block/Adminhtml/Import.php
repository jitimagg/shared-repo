<?php
/**
 * Copyright © Megnor, Inc. All rights reserved.
 */
namespace Fulloop\Products\Block\Adminhtml;

class Import extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'import';
        $this->_headerText = __('Import');
        parent::_construct();
    }
}
