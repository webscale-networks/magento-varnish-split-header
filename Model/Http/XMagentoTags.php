<?php
/**
 * Copyright © Webscale. All rights reserved.
 * See LICENSE for license details.
 */

namespace Webscale\SplitHeader\Model\Http;

use Zend\Http\Header\MultipleHeaderInterface;
use Zend\Http\Header\HeaderValue;
use Zend\Http\Header\GenericHeader;
use Zend\Http\Header\Exception\InvalidArgumentException;

class XMagentoTags implements MultipleHeaderInterface
{
    const HEADER_NAME = 'X-Magento-Tags';

    /** @var string */
    protected $value;

    /**
     * @var HeaderValue
     */
    protected $headerValue;

    /**
     * @param string|null $value
     */
    public function __construct($value = null)
    {
        if ($value) {
            HeaderValue::assertValid($value);
            $this->value = $value;
        }
    }

    /**
     * Create X-Magento-Tags header from a given header line
     *
     * @param string $headerLine The header line to parse.
     * @return self
     * @throws InvalidArgumentException If the name field in the given header line does not match.
     */
    public static function fromString($headerLine)
    {
        list($name, $value) = GenericHeader::splitHeaderLine($headerLine);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== strtolower(self::HEADER_NAME)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid header line for %s string: "%s"',
                    self::HEADER_NAME,
                    $name
                )
            );
        }

        $header = new static($value);

        return $header;
    }

    /**
     * Cast multiple header objects to a single string header
     *
     * @param  array $headers
     * @throws InvalidArgumentException
     * @return string
     */
    public function toStringMultipleHeaders(array $headers)
    {
        $name = $this->getFieldName();
        $values = array($this->getFieldValue());
        foreach ($headers as $header) {
            if (!$header instanceof static) {
                throw new InvalidArgumentException(
                    'This method toStringMultipleHeaders was expecting an array of headers of the same type'
                );
            }
            $values[] = $header->getFieldValue();
        }

        return $name . ': ' . implode(',', $values) . "\r\n";
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return self::HEADER_NAME;
    }

    /**
     * @return string
     */
    public function getFieldValue()
    {
        return $this->value;
    }

    /**
     * Return the header as a string
     *
     * @return string
     */
    public function toString()
    {
        return $this->getFieldName() . ': ' . $this->getFieldValue();
    }
}
