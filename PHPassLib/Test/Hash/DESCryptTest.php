<?php
/**
 * PHP Password Library
 *
 * @package PHPassLib\Tests
 * @author Ryan Chouinard <rchouinard@gmail.com>
 * @copyright Copyright (c) 2012, Ryan Chouinard
 * @license MIT License - http://www.opensource.org/licenses/mit-license.php
 * @version 3.0.0-dev
 */

namespace PHPassLib\Test\Hash;
use PHPassLib\Hash\DESCrypt;

/**
 *
 */
class DESCryptTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Provide valid test vectors.
     *
     * @return array
     */
    public function validTestVectorProvider()
    {
        $vectors = array (
            // From John the Ripper 1.7.9
            array ("U*U*U*U*", 'CCNf8Sbh3HDfQ'),
            array ("U*U***U", 'CCX.K.MFy4Ois'),
            array ("U*U***U*", 'CC4rMpbg9AMZ.'),
            array ("*U*U*U*U", 'XXxzOu6maQKqQ'),
            array ("", 'SDbsugeBiC58A'),

            // From PassLib 1.6
            array ("", 'OgAwTx2l6NADI'),
            array (" ", '/Hk.VPuwQTXbc'),
            array ("test", 'N1tQbOFcM5fpg'),
            array ("Compl3X AlphaNu3meric", 'um.Wguz3eVCx2'),
            array ("4lpHa N|_|M3r1K W/ Cur5Es: #$%(*)(*%#", 'sNYqfOyauIyic'),
            array ("AlOtBsOl", 'cEpWz5IUCShqM'),
        );

        return $vectors;
    }

    /**
     * Provide invalid test vectors.
     *
     * @return array
     */
    public function invalidTestVectorProvider()
    {
        $vectors = array (
            array ("", '!gAwTx2l6NADI', '*0'),
            array ("", '*0', '*1'),
            array ("", '*1', '*0'),
        );

        return $vectors;
    }

    /**
     * Verify that the class produces correct results with valid test vectors.
     *
     * @test
     * @dataProvider validTestVectorProvider
     * @param string $password
     * @param string $hash
     */
    public function validTestVectorsProduceExpectedResults($password, $hash)
    {
        $config = substr($hash, 0, 29);
        $this->assertEquals($hash, DESCrypt::hash($password, $config));
        $this->assertTrue(DESCrypt::verify($password, $hash));
    }

    /**
     * Verify that the class produces correct results with invalid test vectors.
     *
     * @test
     * @dataProvider invalidTestVectorProvider
     * @param string $password
     * @param string $hash
     */
    public function invalidTestVectorsProduceExpectedResults($password, $hash, $errorString)
    {
        $config = substr($hash, 0, 29);
        $this->assertEquals($errorString, DESCrypt::hash($password, $config));
        $this->assertFalse(DESCrypt::verify($password, $hash));
    }

}