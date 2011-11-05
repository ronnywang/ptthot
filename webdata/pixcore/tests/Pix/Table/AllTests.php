<?php

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Pix_Table_AllTests::main');
}

if ($_SERVER['argv'][2]) {
    define('PIXCORE_TEST_DB_CORE', $_SERVER['argv'][2]);
}
require_once(dirname(__FILE__) . '/../../init.inc.php');

class Pix_Table_AllTests
{
    public static function main()
    {
        // Run buffered tests as a separate suite first
        ob_start();
        PHPUnit_TextUI_TestRunner::run(self::suiteBuffered());
        if (ob_get_level()) {
            ob_end_flush();
        }

        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    /**
     * Buffered test suites
     *
     * These tests require no output be sent prior to running as they rely
     * on internal PHP functions.
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suiteBuffered()
    {
        $suite = new PHPUnit_Framework_TestSuite('Pix Framework - Pix - Buffered Test Suites');

        // These tests require no output be sent prior to running as they rely
        // on internal PHP functions

        return $suite;
    }
    /**
     * Regular suite
     *
     * All tests except those that require output buffering.
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Pix Framework - Pix');

	// Start remaining tests...
	$suite->addTestSuite('Pix_Table_Test');
	$suite->addTestSuite('Pix_Table_TestVolumn');
	$suite->addTestSuite('Pix_Table_Test12581');
	$suite->addTestSuite('Pix_Table_Test12583');
	$suite->addTestSuite('Pix_Table_Test13379');
        $suite->addTestSuite('Pix_Table_Test15801');
        if (class_exists('Pix_Table_Cluster')) {
            $suite->addTestSuite('Pix_Table_TestCluster');
        }
        $suite->addTestSuite('Pix_Table_TestRelation');
        if ('mysqlconf' == PIXCORE_TEST_DB_CORE) {
     //       $suite->addTestSuite('Pix_Table_TestDb');
        }
	$suite->addTestSuite('Pix_Table_TestFindBy');
	$suite->addTestSuite('Pix_Table_TestIndex');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Pix_Table_AllTests::main') {
    Pix_Table_AllTests::main();
}

