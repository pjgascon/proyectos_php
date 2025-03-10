<?php
class pushy
{
    const SECRET_KEY = "425bf5d0650eb43211d01484fcf5c26b76d3e3e0d09f19884a02ed4c681a08f6";

    public function comprobarDisponibilidadToken($mToken): bool
    {
        $b = false;

        $fields =   json_encode(array("tokens" => array($mToken)));
        $headers = [
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.pushy.me/devices/presence?api_key=' . self::SECRET_KEY);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        $result = curl_exec($ch);
        curl_close($ch);

        try {
            $mResult = json_decode($result);
            $b = $mResult->presence[0]->online;
        } catch (Exception $e) {
            $b = false;
        }
        return $b;
    }

    public function enviarNotificacion($token, $texto, $quotes_id): bool
    {
        $b = false;

        $headers = [
            'Content-Type: application/json'
        ];
        $fields =   json_encode(array(
            "to" => $token,
            "data" => array(
                "message" => $texto,
                "quotes_id" => $quotes_id
            ),
            "notification" => array(
                "title" => "Waspapp",
                "body" => $texto
            ),
            "content_available " => "true"
        ));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.pushy.me/push?api_key=' . self::SECRET_KEY);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        $result = curl_exec($ch);
        curl_close($ch);

        try {
            $mResult = json_decode($result);
            $b = $mResult->success;
        } catch (Exception $e) {
            $b = false;
        }

        return $b;
    }
}
