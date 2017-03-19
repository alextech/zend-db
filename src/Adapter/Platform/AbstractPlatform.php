<?php
/**
 * @see       http://github.com/zendframework/zend-db for the canonical source repository
 * @copyright Copyright (c) 2015-2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://github.com/zendframework/zend-db/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Db\Adapter\Platform;

abstract class AbstractPlatform implements PlatformInterface
{
    /**
     * @var string[]
     */
    protected $quoteIdentifier = ['"', '"'];

    /**
     * @var string
     */
    protected $quoteIdentifierTo = '\'';

    /**
     * @var bool
     */
    protected $quoteIdentifiers = true;

    /**
     * {@inheritDoc}
     */
    public function quoteIdentifierInFragment($identifier, array $safeWords = [])
    {
        if (! $this->quoteIdentifiers) {
            return $identifier;
        }

        $safeWordsInt = ['*' => true, ' ' => true, '.' => true, 'as' => true];

        foreach ($safeWords as $sWord) {
            $safeWordsInt[strtolower($sWord)] = true;
        }

        $parts = preg_split(
            '/([^0-9,a-z,A-Z$_:])/i',
            $identifier,
            -1,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );

        $identifier = '';

        foreach ($parts as $part) {
            $identifier .= isset($safeWordsInt[strtolower($part)])
                ? $part
                : $this->quoteIdentifier[0]
                . str_replace($this->quoteIdentifier[0], $this->quoteIdentifierTo, $part)
                . $this->quoteIdentifier[1];
        }

        return $identifier;
    }

    /**
     * {@inheritDoc}
     */
    public function quoteIdentifier($identifier)
    {
        return $this->quoteIdentifierChain([$identifier]);
    }

    /**
     * {@inheritDoc}
     */
    public function quoteIdentifierChain($identifierChain)
    {
        if (is_string($identifierChain)) {
            $identifierChain = [$identifierChain];
        }

        if (! $this->quoteIdentifiers) {
            return implode($this->getIdentifierSeparator(), $identifierChain);
        }

        /** @var array $identifierChain */
        foreach ($identifierChain as $key => $identifier) {
            $identifierChain[$key] = str_replace($this->quoteIdentifier[0], $this->quoteIdentifierTo, $identifier);
        }
        $chainGlue = $this->quoteIdentifier[1] . $this->getIdentifierSeparator() . $this->quoteIdentifier[0];

        return $this->quoteIdentifier[0] . implode($chainGlue, $identifierChain) . $this->quoteIdentifier[1];
    }

    /**
     * {@inheritDoc}
     */
    public function getQuoteIdentifierSymbol()
    {
        return $this->quoteIdentifier[0];
    }

    /**
     * {@inheritDoc}
     */
    public function getQuoteValueSymbol()
    {
        return '\'';
    }

    /**
     * {@inheritDoc}
     */
    public function quoteValue($value)
    {
        trigger_error(
            sprintf(
                'Attempting to quote a value in %s without extension/driver support ' .
                'can introduce security vulnerabilities in a production environment',
                get_class($this)
            )
        );
        return '\'' . addcslashes((string) $value, "\x00\n\r\\'\"\x1a") . '\'';
    }

    /**
     * {@inheritDoc}
     */
    public function quoteTrustedValue($value)
    {
        return '\'' . addcslashes((string) $value, "\x00\n\r\\'\"\x1a") . '\'';
    }

    /**
     * {@inheritDoc}
     */
    public function quoteValueList($valueList)
    {
        return implode(', ', array_map([$this, 'quoteValue'], (array) $valueList));
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentifierSeparator()
    {
        return '.';
    }
}
