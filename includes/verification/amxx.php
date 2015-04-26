<?php

$heart->register_payment_api("amxx", "ModuleAmxx");

class ModuleAmxx extends PaymentModule
{

    const SERVICE_ID = "amxx";

    public function verify_sms($sms_code, $sms_number)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            'kod' => $sms_code,
            'id' => $this->data['account_id']
        ));
        curl_setopt($ch, CURLOPT_URL, "http://serwery.amxx.pl/api.php");
        $shop = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if ($shop[0]) {
            $shop[1] = floatval($shop[1]) * 2;
            if ($shop[0] == '2') $output['status'] = "BAD_CODE"; // Bledny kod
            else if ($shop[0] == '3') $output['status'] = "BAD_CODE"; // Juz uzyty
            else if ($shop[0] == '4') $output['status'] = "SERVER_ERROR";
            else if ($shop[1] != floatval(get_sms_cost($sms_number))) {
                $output['status'] = "BAD_NUMBER";
                // Szukamy smsa z kwota rowna $shop[1]
                foreach ($smses as $sms) {
                    if (floatval(get_sms_cost($sms['number'])) == $shop[1]) {
                        $output['tariff'] = $sms['tariff'];
                        break;
                    }
                }
            } else if ($shop[0] == '1') $output['status'] = "OK";
            else $output['status'] = "ERROR";
        } else
            $output['status'] = "NO_CONNECTION";

        return $output;
    }

}

?>
