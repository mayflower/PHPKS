<?php
/*
 * This file is part of PHPKS, a PHP Key Server.
 * Copyright 2015 Peter Ritt <devel AT gagamux.net>
 *
 * PHPKS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PHPKS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PHPKS.  If not, see <http://www.gnu.org/licenses/>.
 */

/*
 * Note: these many tests are justified
 * they may seem redundant and nonsensical
 * but they found me quite some bugs in this simple program
 * unbelievable, what the foo
 */
class PksLookupTest extends PHPUnit_Framework_TestCase {
  public function setUp () {
    $this->curl = null;
    $this->lookupUrl = sprintf("%s%s", TEST_URL_PREFIX, '/pks/lookup?');
    Helper::addTestKey($this, 'rsa-1024-sign-only.asc');
    $this->fingerprint = '4065FEF660A42A63F4E5C6E574C7D32D7662790A';
  }

  public function tearDown () {
    curl_close($this->curl);
  }

  public function test_get_human_readable_200 () {
    $params = sprintf('op=get&search=%s', $this->fingerprint);
    $this->setupCurl($params);
    $actualBody = curl_exec($this->curl);
    Helper::assert_200($this);
    Helper::assert_html($this, $actualBody);

    $regex = '/-----BEGIN PGP PUBLIC KEY BLOCK-----/';
    $this->assertRegexp($regex, $actualBody);

    $regex = '/-----END PGP PUBLIC KEY BLOCK-----/';
    $this->assertRegexp($regex, $actualBody);
  }

  public function test_get_human_readable_400 () {
    $params = 'op=get';
    $this->setupCurl($params);
    Helper::assert_400($this, curl_exec($this->curl));
  }

  public function test_get_human_readable_404 () {
    $params = 'op=get&search=should-not-exist';
    $this->setupCurl($params);
    Helper::assert_404($this, curl_exec($this->curl));
  }

  public function test_get_machine_readable_200 () {
    $params = sprintf('op=get&options=mr&search=%s', $this->fingerprint);
    $this->setupCurl($params);
    $actualBody = curl_exec($this->curl);
    Helper::assert_200($this);
    Helper::assert_plain($this, $actualBody);

    $regex = '/-----BEGIN PGP PUBLIC KEY BLOCK-----/';
    $this->assertRegexp($regex, $actualBody);

    $regex = '/-----END PGP PUBLIC KEY BLOCK-----/';
    $this->assertRegexp($regex, $actualBody);
  }

  public function test_get_machine_readable_400 () {
    $params = 'op=get&options=mr';
    $this->setupCurl($params);
    Helper::assert_400($this, curl_exec($this->curl));
  }

  public function test_get_machine_readable_404 () {
    $params = 'op=get&options=mr&search=should-not-exist';
    $this->setupCurl($params);
    Helper::assert_404($this, curl_exec($this->curl));
  }

  public function test_index_human_readable_200 () {
    $params = sprintf('op=index&search=%s', $this->fingerprint);
    $this->setupCurl($params);
    $actualBody = curl_exec($this->curl);
    Helper::assert_200($this);
    Helper::assert_html($this, $actualBody);

    $regex = '/INDEX "4065FEF660A42A63F4E5C6E574C7D32D7662790A"/';
    $this->assertRegexp($regex, $actualBody);

    $regex = Helper::toRegex("<pre>pub  1024/<a href='/pks/lookup?op=get&search=0x74C7D32D7662790A'>7662790A</a>  2015-03-17 <a href='/pks/lookup?op=get&search=0x74C7D32D7662790A'>RSA 1024 sign only &lt;rsa-1024-sign-only&commat;example&period;org&gt;</a>");
    $this->assertRegexp($regex, $actualBody);

    $regex = '/Fingerprint\=/';
    $this->assertEquals(0, preg_match($regex, $actualBody));
  }

  public function test_index_fingerprint_human_readable_200 () {
    $params = sprintf('op=index&search=%s&fingerprint=on', $this->fingerprint);
    $this->setupCurl($params);
    $actualBody = curl_exec($this->curl);
    Helper::assert_200($this);
    Helper::assert_html($this, $actualBody);

    $regex = '/INDEX "4065FEF660A42A63F4E5C6E574C7D32D7662790A"/';
    $this->assertRegexp($regex, $actualBody);

    $regex = Helper::toRegex("<pre>pub  1024/<a href='/pks/lookup?op=get&search=0x74C7D32D7662790A'>7662790A</a>  2015-03-17 <a href='/pks/lookup?op=get&search=0x74C7D32D7662790A'>RSA 1024 sign only &lt;rsa-1024-sign-only&commat;example&period;org&gt;</a>");
    $this->assertRegexp($regex, $actualBody);

    $regex = '/Fingerprint\=/';
    $this->assertRegexp($regex, $actualBody);
  }

  public function test_index_human_readable_400 () {
    $params = 'op=index';
    $this->setupCurl($params);
    Helper::assert_400($this, curl_exec($this->curl));
  }

  public function test_index_human_readable_404 () {
    $params = 'op=index&search=should-not-exist';
    $this->setupCurl($params);
    Helper::assert_404($this, curl_exec($this->curl));
  }

  public function test_index_machine_readable_200 () {
    $params = sprintf('op=index&options=mr&search=%s', $this->fingerprint);
    $this->setupCurl($params);
    $actualBody = curl_exec($this->curl);
    Helper::assert_200($this);
    Helper::assert_plain($this, $actualBody);

    $regex = Helper::toRegex('info:1:1');
    $this->assertRegexp($regex, $actualBody);

    $regex = Helper::toRegex('pub:74C7D32D7662790A:1:1024:1426620318::');
    $this->assertRegexp($regex, $actualBody);
  }

  public function test_index_machine_readable_400 () {
    $params = 'op=index&options=mr';
    $this->setupCurl($params);
    Helper::assert_400($this, curl_exec($this->curl));
  }

  public function test_index_machine_readable_404 () {
    $params = 'op=index&options=mr&search=should-not-exist';
    $this->setupCurl($params);
    Helper::assert_404($this, curl_exec($this->curl));
  }

  public function test_vindex_human_readable_200 () {
    $params = sprintf('op=vindex&search=%s', $this->fingerprint);
    $this->setupCurl($params);
    $actualBody = curl_exec($this->curl);
    Helper::assert_200($this);
    Helper::assert_html($this, $actualBody);

    $regex = '/VERBOSE INDEX "4065FEF660A42A63F4E5C6E574C7D32D7662790A"/';
    $this->assertRegexp($regex, $actualBody);

    $regex = Helper::toRegex("<pre>pub  1024/<a href='/pks/lookup?op=get&search=0x74C7D32D7662790A'>7662790A</a>  2015-03-17 __________");
    $this->assertRegexp($regex, $actualBody);

    $regex = Helper::toRegex("uid  RSA 1024 sign only &lt;rsa-1024-sign-only&commat;example&period;org&gt;");
    $this->assertRegexp($regex, $actualBody);

    $regex = Helper::toRegex("sig        <a href='/pks/lookup?op=get&search=0x74C7D32D7662790A'>7662790A</a> 2015-03-17 __________ <a href='/pks/lookup?op=get&search=0x74C7D32D7662790A'>RSA 1024 sign only &lt;rsa-1024-sign-only&commat;example&period;org&gt;</a>");
    $this->assertRegexp($regex, $actualBody);

    $regex = '/Fingerprint\=/';
    $this->assertEquals(0, preg_match($regex, $actualBody));
  }

  public function test_vindex_fingerprint_human_readable_200 () {
    $params = sprintf('op=vindex&search=%s&fingerprint=on', $this->fingerprint);
    $this->setupCurl($params);
    $actualBody = curl_exec($this->curl);
    Helper::assert_200($this);
    Helper::assert_html($this, $actualBody);

    $regex = '/VERBOSE INDEX "4065FEF660A42A63F4E5C6E574C7D32D7662790A"/';
    $this->assertRegexp($regex, $actualBody);

    $regex = Helper::toRegex("<pre>pub  1024/<a href='/pks/lookup?op=get&search=0x74C7D32D7662790A'>7662790A</a>  2015-03-17 __________");
    $this->assertRegexp($regex, $actualBody);

    $regex = Helper::toRegex("uid  RSA 1024 sign only &lt;rsa-1024-sign-only&commat;example&period;org&gt;");
    $this->assertRegexp($regex, $actualBody);

    $regex = Helper::toRegex("sig        <a href='/pks/lookup?op=get&search=0x74C7D32D7662790A'>7662790A</a> 2015-03-17 __________ <a href='/pks/lookup?op=get&search=0x74C7D32D7662790A'>RSA 1024 sign only &lt;rsa-1024-sign-only&commat;example&period;org&gt;</a>");
    $this->assertRegexp($regex, $actualBody);

    $regex = '/Fingerprint\=/';
    $this->assertRegexp($regex, $actualBody);
  }

 function test_vindex_human_readable_400 () {
    $params = 'op=vindex';
    $this->setupCurl($params);
    Helper::assert_400($this, curl_exec($this->curl));
  }

  public function test_vindex_human_readable_404 () {
    $params = 'op=vindex&search=should-not-exist';
    $this->setupCurl($params);
    Helper::assert_404($this, curl_exec($this->curl));
  }

  function test_vindex_machine_readable_200 () {
    $params = sprintf('op=vindex&options=mr&search=%s', $this->fingerprint);
    $this->setupCurl($params);
    $actualBody = curl_exec($this->curl);
    Helper::assert_200($this);
    Helper::assert_plain($this, $actualBody);

    $regex = Helper::toRegex('info:1:1');
    $this->assertRegexp($regex, $actualBody);

    $regex = Helper::toRegex('pub:74C7D32D7662790A:1:1024:1426620318::');
    $this->assertRegexp($regex, $actualBody);
  }

  public function test_vindex_machine_readable_400 () {
    $params = 'op=vindex&options=mr';
    $this->setupCurl($params);
    Helper::assert_400($this, curl_exec($this->curl));
  }

  public function test_vindex_machine_readable_404 () {
    $params = 'op=vindex&options=mr&search=should-not-exist';
    $this->setupCurl($params);
    Helper::assert_404($this, curl_exec($this->curl));
  }

  public function setupCurl ($params) {
    $this->curl = curl_init();
    curl_setopt_array($this->curl, array(
      CURLOPT_HTTPGET         => true,
      CURLOPT_RETURNTRANSFER  => true,
      CURLOPT_URL             => sprintf("%s%s", $this->lookupUrl, $params),
    ));
  }
}