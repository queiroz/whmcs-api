<?php namespace Queiroz\WhmcsApi;

class WhmcsCurl
{
    public function request($url, $params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $data = curl_exec($ch);

        if (curl_error($ch)) {
            throw new ConnectionException("Connection Error: " . curl_errno($ch) . ' - ' . curl_error($ch));
        }

        curl_close($ch);

        return $data;
    }
}