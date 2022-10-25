<?php

namespace Devteam\Payon;

use Devteam\Payon\PayonEncrypto;

class PayonHelper
{

    public function __construct()
    {
        $url_base = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        $this->ref_code = 'MCAPI-WPV1-'. $url_base;
    }

    /**
     * @param $param
     * @return mixed
     */
    function CreateOrderPaynow($param, $secret_key, $app_id, $url, $mc_auth, $mc_pass)
    {
        $data = $param;
        $data = json_encode($data);
        $crypto = new PayonEncrypto($secret_key);
        $data = $crypto->Encrypt($data);
        $checksum = md5($app_id . $data . $secret_key);
        $bodyPost = array(
            'app_id' => $app_id,
            'data' => $data,
            'checksum' => $checksum,
            'ref_code' => $this->ref_code
        );
        $result = $this->call($bodyPost, "createOrderPaynow", $url, $mc_auth, $mc_pass);
        return $result;
    }

    /**
     * @param $input
     * @return mixed
     */
    function CheckPayment($input, $secret_key, $app_id, $url, $mc_auth, $mc_pass)
    {
        $data = array(
            'merchant_request_id' => $input,
        );
        $data = json_encode($data);
        $crypto = new PayonEncrypto($secret_key);
        $data = $crypto->Encrypt($data);
        $checksum = md5($app_id . $data . $secret_key);
        $bodyPost = array(
            'app_id' => $app_id,
            'data' => $data,
            'checksum' => $checksum,
            'ref_code' => $this->ref_code
        );
        $result = $this->call($bodyPost, "checkPayment", $url, $mc_auth, $mc_pass);
        return $result;
    }

    /**
     * @param string $param
     * @return mixed
     */
    function GetBankInstallment($param = "", $secret_key, $app_id, $url, $mc_auth, $mc_pass)
    {
        $data = array();
        $data = json_encode($data);
        $crypto = new PayonEncrypto($secret_key);
        $data = $crypto->Encrypt($data);
        $checksum = md5($app_id . $data . $secret_key);
        $bodyPost = array(
            'app_id' => $app_id,
            'data' => $data,
            'checksum' => $checksum,
            'ref_code' => $this->ref_code
        );
        $result = $this->call($bodyPost, "getBankInstallmentV2", $url, $mc_auth, $mc_pass);
        return $result;
    }

    /**
     * @param $data
     * @return mixed
     */
    function getFee($data, $secret_key, $app_id, $url, $mc_auth, $mc_pass)
    {
        $data = json_encode($data);
        $crypto = new PayonEncrypto($secret_key);
        $data = $crypto->Encrypt($data);
        $checksum = md5($app_id . $data . $secret_key);
        $bodyPost = array(
            'app_id' => $app_id,
            'data' => $data,
            'checksum' => $checksum,
            'ref_code' => $this->ref_code
        );
        $result = $this->call($bodyPost, "getFeeInstallmentv2", $url, $mc_auth, $mc_pass);
        return $result;
    }

    /**
     * @param $data
     * @return mixed
     */
    function createOrderInstallment($data, $secret_key, $app_id, $url, $mc_auth, $mc_pass)
    {
        $data = json_encode($data);
        $crypto = new PayonEncrypto($secret_key);
        $data = $crypto->Encrypt($data);
        $checksum = md5($app_id . $data . $secret_key);
        $bodyPost = array(
            'app_id' => $app_id,
            'data' => $data,
            'checksum' => $checksum,
            'ref_code' => $this->ref_code
        );
        $result = $this->call($bodyPost, "createOrderInstallment", $url, $mc_auth, $mc_pass);
        return $result;
    }

    /**\
     * @param $params
     * @param $fnc
     * @return mixed
     */
    function Call($params, $fnc, $url, $mc_auth, $mc_pass)
    {
        if(substr( $url,-1) != '/'){
            $url = $url.'/';
        }
        $url = $url.$fnc;
        // $response = curl_exec($curl);
        $agent = $_SERVER["HTTP_USER_AGENT"];
        if(empty($agent))
        {
            $agent = 'not user agent';
        }
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_USERAGENT, $agent);
        curl_setopt($curl, CURLOPT_USERPWD, $mc_auth . ':' . $mc_pass);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Accept: application/json',
                'Content-Type: application/json'
            )
        );
        $response = curl_exec($curl);
        $resultStatus = curl_getinfo($curl);

        if($resultStatus['http_code'] == 200 && isset($response) )
        {
            $response = json_decode($response, true);
            // return $response['data'];
            if ($response['error_code'] == "00") {
                return $response;
            } else {
                return false;
            }
        } else{
            return false;
        }
    }
}
