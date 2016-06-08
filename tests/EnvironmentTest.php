<?php

use Scoop\Environment;

/**
 * Class EnvironmentTest
 */
class EnvironmentTest extends PHPUnit_Framework_TestCase {

    public function test_get_environment () {

        $ogServer = $_SERVER;

        $_SERVER['SERVER_NAME'] = 'localhost.jor.pw';
        $this->assertEquals(Environment::ENV_LOCAL, Environment::get_environment(), "localhost.jor.pw should be marked as local env");

        $_SERVER['SERVER_NAME'] = 'test.jor.pw';
        $this->assertEquals(Environment::ENV_TEST, Environment::get_environment(), "test.jor.pw should be marked as test env");

        $_SERVER['SERVER_NAME'] = 'staging.jor.pw';
        $this->assertEquals(Environment::ENV_STAGING, Environment::get_environment(), "staging.jor.pw should be marked as staging env");

        $_SERVER['SERVER_NAME'] = 'jor.pw';
        $this->assertEquals(Environment::ENV_PROD, Environment::get_environment(), "jor.pw should be marked as production env");

        $_SERVER = $ogServer;

    }

    public function test_get_server_name () {

        $ogServer = $_SERVER;

        unset($_SERVER['SERVER_NAME']);
        unset($_SERVER['HOSTNAME']);

        $this->assertEquals(gethostname(), Environment::get_server_name(), "empty \$_SERVER variables should return hostname()");

        $_SERVER['HOSTNAME'] = 'hostname';
        $this->assertEquals('hostname', Environment::get_server_name(), "expected value in \$_SERVER['HOSTNAME']");

        $_SERVER['SERVER_NAME'] = 'servername';
        $this->assertEquals('servername', Environment::get_server_name(), "expected value in \$_SERVER['SERVER_NAME']");

        $_SERVER = $ogServer;
    }

    public function test_is_internal_ip () {

        $clientIp = $serverIp = '0.0.0.0';
        $this->assertTrue(Environment::is_internal_ip($clientIp, $serverIp), "same ip should be marked as internal");

        $serverIp = '111.111.111.111';

        $clientIp = '10.0.0.0';
        $this->assertTrue(Environment::is_internal_ip($clientIp, $serverIp), "10.0.0.0 client ip should be marked as internal");

        $clientIp = '172.16.0.0';
        $this->assertTrue(Environment::is_internal_ip($clientIp, $serverIp), "172.16.0.0 client ip should be marked as internal");

        $clientIp = '192.168.0.0';
        $this->assertTrue(Environment::is_internal_ip($clientIp, $serverIp), "192.168.0.0 client ip should be marked as internal");

        $clientIp = '22.22.22.22';
        $this->assertFalse(Environment::is_internal_ip($clientIp, $serverIp), "public and different client and server ips should be marked as external");

        $clientIp = '1.1';
        $this->assertFalse(Environment::is_internal_ip($clientIp, $serverIp), "client ip without 4 octets should be marked as external");

    }

    public function test_constant_is_defined_and_equals () {

        $value = rand(1,99999);
        $constName = 'constant_is_defined_and_equals'.$value;

        $this->assertFalse(Environment::constant_is_defined_and_equals($constName, $value + 1), "constant '{$constName}' should't exist yet");
        $this->assertFalse(Environment::constant_is_defined_and_equals($constName, $value), "constant '{$constName}' shouldn't exist yet");

        define($constName, $value);
        $this->assertTrue(Environment::constant_is_defined_and_equals($constName, $value), "constant '{$constName}' should equal {$value}");
    }
}
