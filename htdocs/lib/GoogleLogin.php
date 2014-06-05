<?php
/**
* GoogleLogin.php
* Helper for implementation of Google Oath2
*
* Description
* @package Replex Crash Processor
* @author Latif Khalifa <latifer@streamgrid.net>
* @copyright Copyright &copy; 2012-2014, Latif Khalifa
* 
* Permission is hereby granted, free of charge, to any person obtaining
* a copy of this software and associated documentation files
* (the "Software"), to deal in the Software without restriction, including
* without limitation the rights to use, copy, modify, merge, publish,
* distribute, sublicense, and/or sell copies of the Software, and to permit
* persons to whom the Software is furnished to do so, subject to the
* following conditions:
*
* - The above copyright notice and this permission notice shall be included
* in all copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
* EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
* MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
* IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
* DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
* OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
* OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
* 
*/


class GoogleLogin
{
    static $client = null;
    static $clientID;
    static $clientSecret;
    static $scope = "openid email";
    static $accessToken;
    
    static function init($clientID, $clientSecret)
    {
        self::$clientID = $clientID;
        self::$clientSecret = $clientSecret;
    }
    
    static function getClient()
    {
        if (self::$client == null)
        {
            $client = new Google_Client();
            $client->setClientId(self::$clientID);
            $client->setClientSecret(self::$clientSecret);
            $client->setRedirectUri(URL_ROOT . "/process_login.php" );
            $client->setScopes("email");
            self::$client = $client;
        }

        return self::$client;
    }
    
    static function loginURL()
    {
        $client = self::getClient();
        return $client->createAuthUrl();
    }
    
    static function verifyLogin($code)
    {
        try
        {
            $client = self::getClient();
            $client->authenticate($code);
            self::$accessToken = $client->getAccessToken();
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    
    static function getAttribs()
    {
        $client = self::getClient();
        $attr = $client->verifyIdToken()->getAttributes();
        return $attr["payload"];
    }
    
    static function userID()
    {
        $attr = self::getAttribs();
        return $attr["sub"];
    }
    
    static function userEmail()
    {
        $attr = self::getAttribs();
        return $attr["email"];
    }
    
    
}