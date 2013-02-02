<?php

require_once(dirname(__FILE__) . "/../lib/analytics/client.php");

class SocketConsumerTest extends PHPUnit_Framework_TestCase {

  private $client;

  function setUp() {
    $this->client = new Analytics_Client("testsecret",
                                          array("consumer" => "socket"));
  }

  function testTrack() {
    $tracked = $this->client->track("some_user", "Socket PHP Event");
    $this->assertTrue($tracked);
  }

  function testIdentify() {
    $identified = $this->client->identify("some_user", array(
                    "name"      => "Calvin",
                    "loves_php" => false,
                    "birthday"  => time(),
                    ));

    $this->assertTrue($identified);
  }

  function testShortTimeout() {
    $client = new Analytics_Client("testsecret",
                                   array( "timeout"  => 0.01,
                                          "consumer" => "socket" ));

    $tracked = $client->track("some_user", "Socket PHP Event");
    $this->assertTrue($tracked);

    $identified = $client->identify("some_user");
    $this->assertTrue($identified);
  }

  function testProductionProblems() {
    $client = new Analytics_Client("x", array(
        "consumer"      => "socket",
        "error_handler" => function () { throw new Exception("Was called"); }));

    # Shouldn't error out without debug on.
    $client->track("some_user", "Socket PHP Event");
    $client->__destruct();
  }

  function testDebugProblems() {

    $options = array(
      "debug"         => true,
      "consumer"      => "socket",
      "error_handler" => function ($errno, $errmsg) {
                            if ($errno != 400)
                              throw new Exception("Response is not 400"); }
    );

    $client = new Analytics_Client("x", $options);

    # Should error out with debug on.
    $client->track("some_user", "Socket PHP Event");
    $client->__destruct();
  }
}
?>