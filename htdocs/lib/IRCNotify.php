<?php
class IRCNotify
{
    function http_post($url, $fields)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $res = curl_exec($ch);
        curl_close($ch);

        return $res;
    }
    
    function send($chan, $msg)
    {
        $fields = array(
                  "chan" => $chan,
                  "auth" => file_get_contents(SITE_ROOT . '/lib/irc_passwd'),
                  "msg"  => $msg
                  );
        
        self::http_post("http://messi.streamgrid.net/~lkalif/lolabot.php", $fields);
    }
}