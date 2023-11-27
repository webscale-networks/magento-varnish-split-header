<?php
/**
 * Copyright © Webscale. All rights reserved.
 * See LICENSE for license details.
 */

namespace Webscale\SplitHeader\Plugin;

use Magento\Framework\HTTP\PhpEnvironment\Response as Subject;
use Zend\Http\HeaderLoader;
use Webscale\SplitHeader\Model\Http\XMagentoTags;
use Webscale\SplitHeader\Helper\Config;

class HttpResponseSplitHeader
{
    /**
     * Approximately 8kb
     *
     * @var int
     */
    private $maxSize = 8000;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Special case for handling X-Magento-Tags header
     * splits very long header into multiple headers
     *
     * @param Subject  $subject
     * @param \Closure $proceed
     * @param string   $name
     * @param string   $value
     * @param bool     $replace
     *
     * @return Subject|mixed
     */
    public function aroundSetHeader(
        Subject $subject,
        \Closure $proceed,
        $name,
        $value,
        $replace = false
    ) {
        if (!$this->config->isSplitEnabled() || !$this->config->isVarnish()) {
            return $proceed($name, $value, $replace);
        }

        $this->addHeaderToStaticMap();

        if ($name == XMagentoTags::HEADER_NAME) {
            $headerLength = 0;
            $value = (string)$value;
            $tags = explode(',', $value);

            $newTags = [];
            foreach ($tags as $tag) {
                if ($headerLength + strlen($tag) > $this->maxSize - count($tags) - 1) {
                    $tagString = implode(',', $newTags);
                    $subject->getHeaders()->addHeaderLine($name, $tagString);
                    $newTags = [];
                    $headerLength = 0;
                }
                $headerLength += strlen($tag);
                $newTags[] = $tag;
            }

            // Add remaining tags to header or when they do not reach the limit at all
            if (count($newTags) > 0) {
                $tagString = implode(',', $newTags);
                $subject->getHeaders()->addHeaderLine($name, $tagString);
            }

            return $subject;
        }

        return $proceed($name, $value, $replace);
    }

    /**
     * Add X-Magento-Tags header to HeaderLoader static map
     *
     * @return void
     */
    private function addHeaderToStaticMap()
    {
        HeaderLoader::addStaticMap(
            [
                'xmagentotags' => XMagentoTags::class,
            ]
        );
    }
}
