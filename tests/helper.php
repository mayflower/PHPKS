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

class Helper {
  public static function toRegex ($text) {
    return sprintf('/%s/', preg_quote($text, '/'));
  }

  public static function addTestKey ($scope, $fileName) {
    $keyFile = sprintf("%s/%s", KEYRING_DIR, $fileName);
    $cmd = sprintf(
      "%s --homedir %s --import --no-permission-warning %s %s 2>/dev/null",
      escapeshellarg(GPG_BINARY),
      escapeshellarg(KEYRING_DIR),
      '--utf8-strings --display-charset=utf-8',
      escapeshellarg($keyFile)
    );
    $output = array();
    $rc = null;
    exec($cmd, $output, $rc);
    $scope->assertEquals(0, $rc);
  }

  public static function removeTestKey ($scope, $fingerprint) {
    // NOTE: this fails if there is a secret key for the key to delete
    // NOTE: never have any secret keys on any keyserver
    $cmd = sprintf(
      "%s --homedir %s --batch --delete-key %s 2>/dev/null",
      escapeshellarg(GPG_BINARY),
      escapeshellarg(KEYRING_DIR),
      escapeshellarg($fingerprint)
    );
    exec($cmd);
    $output = array();
    $rc = null;

    $scope->assertTrue(in_array($rc, array(0, 2)));
  }

  public static function removeHugeImportKeys ($scope) {
    // keys in huge-import.asc
    $fingerprints = array(
      "C4F9EFA1788E732116545E25ED3BD8286AD570D8",
      "2357D6B2B6B7488FF927477F07399B480E3359BE",
      "53D3F9E76AACC1B7C8D1112DE65A8144FBDC727B",
      "59BE8AFF1E4A99D64F0D72D822E236FA5C9B0BFC",
      "7C289B5B50EB330C967D9804015CFEAC10B81487",
      "AF7AFDE2E1AF23862F475F3903AD88C20B138D1F",
      "3A3140562C4D2A6FC0F2BDF74464A0EBBAC3A439",
      "84ACAB116AE3ED83620F3836984AD0897CB68A59",
      "66F4D3C8B2BCA0D2D19F337BA77885BCED4867B3",
      "5EB6F5761BED79C36B7C7171FB44AF967C3C0E1A",
      "619F0AE3AE73EB3048820D4DEB7058862845DA36",
      "46C20795800A64713E007717AFC999E50CBE05E4",
      "DB19164C0CDB8AB579A8A0F5F43F7007FD876243",
      "93B8717F8C8F57DF6BE0301EAD7D52B4F2937D35",
      "1898118650F39DEEB03A8241CB6A8CCF670EC450",
      "7430FA14FBB90E9085C2D0AE9389403D7275C624",
      "80DD4259C5BACCAEA99EC6717CE9E27401BECFE1",
      "DA64F34C1C62FCFF6C3688A675E0206854BD8F79",
      "2DFA59FEC2A095B47AA6876DFA240154351B3AFC",
      "56CB94B6294BD942CA07AD7932553DD1CA90CF78",
      "20E44BE1BDFE8E854F4CDCE7C901C59F1F5D2A84",
      "EE00ADEBAE93C518B15FA57EAEC94BAF5A7CA6AD",
      "C4B02636A9AE0DE3724E9F02F987D2F0690ECA3B",
      "B9BFAB65BB08A1D4787E3AEC2388D4374BF7BFED",
      "81B7674B1AE5E660D9CE5D2D16CAF789F53BE342",
      "1F5790BD6768163243221E60D10FD2B1E530023F",
      "67A44EDB3474B1CC70086C304BB99A442108EAFF",
      "0E2F79339D0F0FCC51A046D08AF247C2BCB25823",
      "294B60B9A79DCAE45848CFA3D9A3BAD06CD5E8BB",
      "EE605D4FB04127A73FD955772F2FF4490FE49C15",
      "E4F6DC487662EE567AAEE4D0640F14B0CE68588E",
      "894011EC6411306F15C965BE9E720129BC924BDE",
      "2658F05CA3F97288D6C3E9285CED67D9D1976BCE",
      "7E6DEB74F556A058EF7E8835D4D4744AB859B940",
      "71489591DAA094B273B5D1CB6851A2C272F27E28",
      "34C0EF53D535CF70F76FBC1DC4295C3DB6EA9CBC",
      "B40629CE080973108171F48BE4FE90AA17E7BBD1",
      "4EE63F4C9AA69649FB91DEE53D9F1941D66D5505",
      "D4310237CFD379ADA6D71D3767CACA90334B5C2C",
      "4020A2879D503BC170C39FF57618646FB2F6D669",
      "5317EC731762A5AD131C400C479927F7074C2A95",
      "CE1D114F9D0997968E9F6DB453A650AC4CF8E552",
      "697B514351DB4B56FACA9DE1C1484F38ABACA04A",
      "19CF5BF1C2BA0EB76A1F0AF76BEDD4EB33876261",
      "006627A9544D30B776AA921A197E8DF430EE4BEB",
      "A3BF99B92E0BCF8DBDE921C9176725343013E832",
      "592541221B52389CD7913FFF82CBA12F3A6B0791",
      "89BE8F3FC4EBAC124299C902C884D84ABF624F3C",
      "496A779A5FEAB3CB27B52049C0D29A07CA85B132",
      "61D3909DCE896BC8203FA15577C5DE5588D34B38",
    );
    foreach ($fingerprints as $f) {
      self::removeTestKey($scope, $f);
    }
  }

  public static function assert_200 ($scope) {
    $actual = curl_getinfo($scope->curl, CURLINFO_HTTP_CODE);
    $scope->assertEquals(200, $actual);
  }

  public static function assert_301 ($scope, $expectedLocation) {
    $actual = curl_getinfo($scope->curl, CURLINFO_HTTP_CODE);
    $scope->assertEquals(301, $actual);

    $actual = curl_getinfo($scope->curl, CURLINFO_REDIRECT_URL);
    $scope->assertEquals($expectedLocation, $actual);
  }

  public function assert_400 ($scope, $actualBody) {
    $actual = curl_getinfo($scope->curl, CURLINFO_HTTP_CODE);
    $scope->assertEquals(400, $actual);

    Helper::assert_html($scope, $actualBody);

    $regex = '/invalid\/missing .+ param/';
    $scope->assertRegexp($regex, $actualBody);
  }

  public function assert_404 ($scope, $actualBody) {
    $actual = curl_getinfo($scope->curl, CURLINFO_HTTP_CODE);
    $scope->assertEquals(404, $actual);

    Helper::assert_html($scope, $actualBody);

    $regex = '/No results found/';
    $scope->assertRegexp($regex, $actualBody);
  }

  public function assert_html ($scope, $actualBody) {
    $actual = curl_getinfo($scope->curl, CURLINFO_CONTENT_TYPE);
    $scope->assertRegexp('/text\/html/', $actual);

    $regex = '/\<body\>/';
    $scope->assertRegexp($regex, $actualBody);
  }

  public function assert_plain ($scope, $actualBody) {
    $actual = curl_getinfo($scope->curl, CURLINFO_CONTENT_TYPE);
    $scope->assertRegexp('/text\/plain/', $actual);

    $regex = '/\<body\>/';
    $scope->assertEquals(0, preg_match($regex, $actualBody));
  }
}