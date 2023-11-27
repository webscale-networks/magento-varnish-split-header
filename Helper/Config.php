<?php
/**
 * Copyright © Webscale. All rights reserved.
 * See LICENSE for license details.
 */

namespace Webscale\SplitHeader\Helper;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Module\ModuleListInterface;
use Webscale\Varnish\Helper\Config as CoreConfig;
use Magento\PageCache\Model\Config as CacheConfig;
use Magento\Framework\App\Helper\Context;
use Webscale\Varnish\Logger\Logger;

class Config extends CoreConfig
{
    public const XML_PATH_SPLIT_ENABLED = 'webscale_varnish/general/split_enabled';

    /** @var CacheConfig $cacheConfig */
    protected $cacheConfig;

    /**
     * @param Context $context
     * @param ModuleListInterface $moduleList
     * @param Logger $logger
     */
    public function __construct(
        Context $context,
        ModuleListInterface $moduleList,
        WriterInterface $writerInterface,
        Logger $logger,
        CacheConfig $cacheConfig
    ) {
        $this->moduleList = $moduleList;
        $this->writerInterface = $writerInterface;
        $this->logger = $logger;

        parent::__construct($context, $moduleList, $writerInterface, $logger);

        $this->cacheConfig = $cacheConfig;
    }

    /**
     * Check if split tags is enabled
     *
     * @return bool
     */
    public function isSplitEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(self::XML_PATH_SPLIT_ENABLED);
    }

    /**
     * Check if varnish is a current caching solution
     *
     * @return bool
     */
    public function isVarnish()
    {
        return ($this->cacheConfig->isEnabled() && $this->cacheConfig->getType() == CacheConfig::VARNISH);
    }
}
