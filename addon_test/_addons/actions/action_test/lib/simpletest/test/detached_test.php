<?php
    // $Id: detached_test.php 135 2009-02-08 16:57:41Z anw $
    require_once('../detached.php');
    require_once('../reporter.php');

    // The following URL will depend on your own installation.
    $command = 'php ' . dirname(__FILE__) . '/visual_test.php xml';

    $test = &new TestSuite('Remote tests');
    $test->addTestCase(new DetachedTestCase($command));
    if (SimpleReporter::inCli()) {
        exit ($test->run(new TextReporter()) ? 0 : 1);
    }
    $test->run(new HtmlReporter());
?>