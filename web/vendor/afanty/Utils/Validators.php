<?php

namespace Afanty\Utils;


/**
 * Validation utilities.
 */
class Validators
{
	/**
	 * Finds whether a value is an integer.
	 * @return bool
	 */
	public static function isNumericInt($value)
	{
		return is_int($value) || is_string($value) && preg_match('#^-?[0-9]+\z#', $value);
	}


	/**
	 * Finds whether a string is a floating point number in decimal base.
	 * @return bool
	 */
	public static function isNumeric($value)
	{
		return is_float($value) || is_int($value) || is_string($value) && preg_match('#^-?[0-9]*[.]?[0-9]+\z#', $value);
	}


	/**
	 * Finds whether a value is a syntactically correct callback.
	 * @return bool
	 */
	public static function isCallable($value)
	{
		return $value && is_callable($value, TRUE);
	}


	/**
	 * Finds whether a value is an UTF-8 encoded string.
	 * @param  string
	 * @return bool
	 */
	public static function isUnicode($value)
	{
		return is_string($value) && preg_match('##u', $value);
	}


	/**
	 * Finds whether a value is "falsy".
	 * @return bool
	 */
	public static function isNone($value)
	{
		return $value == NULL; // intentionally ==
	}


	/**
	 * Finds whether a variable is a zero-based integer indexed array.
	 * @param  array
	 * @return bool
	 */
	public static function isList($value)
	{
		return Arrays::isList($value);
	}


	/**
	 * Is a value in specified range?
	 * @param  mixed
	 * @param  array  min and max value pair
	 * @return bool
	 */
	public static function isInRange($value, $range)
	{
		return (!isset($range[0]) || $range[0] === '' || $value >= $range[0])
			&& (!isset($range[1]) || $range[1] === '' || $value <= $range[1]);
	}



	/**
	 * Finds whether a string is a valid URI according to RFC 1738.
	 * @param  string
	 * @return bool
	 */
	public static function isUri($value)
	{
        //var_dump($value);exit;
		return (bool) preg_match('#^[a-z\d+\.-]+:\S+\z#i', $value);
	}

    /**
     * 是否合法的uri path 参数
     * @param string
     *
     * @return bool
     */
    public static function isUriPath($value)
    {
        return (bool) preg_match('#^[a-zA-Z\-_0-9\.]+$#', $value);
    }

	/**

	 * Checks whether the input is a class, interface or trait.
	 * @param  string
	 * @return bool
	 */
	public static function isType($type)
	{
		return class_exists($type) || interface_exists($type) || (PHP_VERSION_ID >= 50400 && trait_exists($type));
	}


	/**
	 * Checks whether the input is a valid PHP identifier.
	 * @return bool
	 */
	public static function isPhpIdentifier($value)
	{
		return is_string($value) && preg_match('#^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\z#', $value);
	}

}
