<?php


namespace CDesign\ArrayToString;

use InvalidArgumentException;

class Converter
{
    protected static $depth = 0;
    protected static $ident;
    protected static $openTag = 'array(';
    protected static $closeTag = ')';

    /**
     * @param array  $data
     * @param string $indent
     * @param bool   $inline
     * @param bool   $shortSyntax
     *
     * @return string
     */
    public static function convert(array $data, $indent = "\t", $inline = false, $shortSyntax = true)
    {
        self::$depth = 0;
        self::$ident = $inline ? false : $indent;

        if ($shortSyntax) {
            self::$openTag = '[';
            self::$closeTag = ']';
        }

        $str = static::renderArray($data);
        return $inline ? preg_replace("/[\n\r]/", '', $str) : $str;
    }

    /**
     * @param array $data
     *
     * @return string
     */
    protected static function renderArray(array $data)
    {
        self::$depth++;

        $result = '';
        $counter = 0;
        foreach ($data as $key => $value) {
            $counter++;

            $result .= self::renderIndent(self::$depth + 1);
            $result .= is_int($key) ? $key : sprintf('\'%s\'', $key);
            $result .= self::$ident === false ? '=>' : ' => ';
            $result .= self::renderValue($value);
            $result .= count($data) === $counter ? '' : ',' . PHP_EOL;
        }

        self::$depth--;

        return self::$openTag . PHP_EOL . $result . PHP_EOL . self::renderIndent(self::$depth + 1) . self::$closeTag;
    }

    /**
     * @param int $multiple
     *
     * @return string
     */
    protected static function renderIndent($multiple)
    {
        return str_repeat(self::$ident, $multiple);
    }

    /**
     * @param $item
     *
     * @return string
     */
    protected static function renderValue($item)
    {
        $type = gettype($item);
        switch ($type) {
            case 'boolean':
                return $item ? 'true' : 'false';
            case 'double':
            case 'integer':
                return $item;
            case 'string':
                return sprintf('\'%s\'', $item);
            case 'object':
                return self::renderObject($item);
            case 'array':
                return self::renderArray($item);
            case 'NULL':
                return 'null';
            default:
                throw new InvalidArgumentException(sprintf('Invalid type %s', $type));
        }
    }

    /**
     * @param $object
     *
     * @return string
     */
    protected static function renderObject($object)
    {
        if (method_exists($object, 'toArray')) {
            return static::renderValue($object->toArray());
        }

        if ($object instanceof \JsonSerializable) {
            return static::renderValue($object->jsonSerialize());
        }

        throw new InvalidArgumentException(sprintf('Object %s can\'t be converted', get_class($object)));
    }
}