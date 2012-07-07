<?php
/**
 * PHP Password Library
 *
 * @package PHPassLib\Hashes
 * @author Ryan Chouinard <rchouinard@gmail.com>
 * @copyright Copyright (c) 2012, Ryan Chouinard
 * @license MIT License - http://www.opensource.org/licenses/mit-license.php
 * @version 3.0.0-dev
 */

namespace PHPassLib\Hash;
use PHPassLib\Hash,
    PHPassLib\Utilities,
    PHPassLib\Exception\InvalidArgumentException;

/**
 * DES Crypt Module
 *
 * This class provides an interface to the legacy Unix DES-based algorithm.
 * The algorithm is considered weak by modern standards and should not be used
 * for new applications. It is only provided here for completeness.
 *
 *<code>
 * <?php
 * use PHPassLib\Hash\DESCrypt;
 *
 * $hash = DESCrypt::hash($password);
 * if (DESCrypt::verify($password, $hash)) {
 *     // Password matches, user is authenticated
 * }
 * </code>
 *
 * @link http://en.wikipedia.org/wiki/Crypt_(Unix)#Traditional_DES-based_scheme
 *     DES Crypt implementation in UNIX crypt() at Wikipedia
 */
class DESCrypt implements Hash
{

    /**
     * Generate a config string suitable for use with module hashes.
     *
     * Available options:
     *  - salt: If provided, must be a 2-character string containing only
     *      characters from ./0-9A-Za-z. It is recommended to omit this option
     *      and let the class generate one for you.
     *
     * @param array $config Array of configuration options.
     * @return string Configuration string in the format
     *     "<salt><checksum>".
     * @throws InvalidArgumentException Throws an InvalidArgumentException if
     *     any passed-in configuration options are invalid.
     */
    public static function genConfig(Array $config = array ())
    {
        $defaults = array (
            'salt' => Utilities::encode64(Utilities::genRandomBytes(1)),
        );
        $config = array_merge($defaults, array_change_key_case($config, CASE_LOWER));

        $value = '*1';
        if (self::validateOptions($config)) {
            $value = $config['salt'];
        }

        return $value;
    }

    /**
     * Generate a hash using a pre-defined config string.
     *
     * @param string $password Password string.
     * @param string $config Configuration string.
     * @return string Returns the hash string on success. On failure, one of
     *     *0 or *1 is returned.
     */
    public static function genHash($password, $config)
    {
        $hash = crypt($password, $config);
        if (!preg_match('/^[\.\/0-9A-Za-z]{13}$/', $hash)) {
            $hash = ($config == '*0') ? '*1' : '*0';
        }

        return $hash;
    }

    /**
     * Generate a hash using either a pre-defined config string or an array.
     *
     * @param string $password Password string.
     * @param string|array $config Optional config string or array of options.
     * @return string Encoded password hash.
     */
    public static function hash($password, $config = array ())
    {
        if (is_array($config)) {
            $config = self::genConfig($config);
        }

        return self::genHash($password, $config);
    }

    /**
     * Verify a password against a hash string.
     *
     * @param string $password Password string.
     * @param string $hash Hash string.
     * @return boolean Returns true if the password matches, false otherwise.
     */
    public static function verify($password, $hash)
    {
        return ($hash === self::hash($password, $hash));
    }

    /**
     * Validate a set of module options.
     *
     * @param array $options Associative array of options.
     * @return boolean Returns true if all options are valid.
     * @throws InvalidArgumentException Throws an InvalidArgumentException
     *     if an invalid option value is encountered.
     */
    protected static function validateOptions(Array $options)
    {
        $options = array_change_key_case($options, CASE_LOWER);
        foreach ($options as $option => $value) switch ($option) {

            case 'salt':
                if (!preg_match('/^[\.\/0-9A-Za-z]{2}$/', $value)) {
                    throw new InvalidArgumentException('Salt must be a string matching the regex pattern /[./0-9A-Za-z]{2}/.');
                }
                break;

            default:
                break;

        }

        return true;
    }

}