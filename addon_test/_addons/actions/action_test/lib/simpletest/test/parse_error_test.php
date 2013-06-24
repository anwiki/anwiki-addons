<?php
    // $Id: parse_error_test.php 135 2009-02-08 16:57:41Z anw $
    
    require_once('../unit_tester.php');
    require_once('../reporter.php');

    $test = &new TestSuite('This should fail');
    $test->addTestFile('test_with_parse_error.php');
    $test->run(new HtmlReporter());
?>