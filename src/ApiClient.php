<?php

/*
 * Leeloo REST API Client
 * Roman WebDS  info@webds.net  Telegram WebDS_Net
 * Documentation
 * https://leelooai.atlassian.net/wiki/spaces/DOC/overview
 *
 */

namespace Webds\Leeloo;
use stdClass;

class ApiClient implements ApiInterface
{

    private $apiUrl = 'https://api.leeloo.ai/api/v1';

    private $TokenRead;
    private $TokenWrite;

    /**
     * Sendpulse API constructor
     *
     * @param                       $userId
     * @param                       $secret
     * @param TokenStorageInterface $tokenStorage
     *
     * @throws Exception
     */
    public function __construct($TokenWrite,$TokenRead = "")
    {

        if (empty($TokenWrite)) {
          return false;//('Empty ID or TokenWrite');
        }

        $this->TokenRead = $TokenRead;
        $this->TokenWrite = $TokenWrite;
    }



    /**
     * Form and send request to API service
     *
     * @param        $path
     * @param string $method
     * @param array $data
     * @param bool $useToken
     *
     * @return stdClass
     */
    protected function sendRequest($path, $method = 'GET', $data = array(), $useToken = true)
    {
        $url = $this->apiUrl . '/' . $path;
        $method = strtoupper($method);
        $curl = curl_init();

        if ($useToken && !empty($this->token)) {
            $headers = array('X-Leeloo-AuthToken: ' . $this->TokenWrite);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }

        switch ($method) {
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, count($data));
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                break;
            case 'PUT':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                break;
            case 'DELETE':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                break;
            default:
                if (!empty($data)) {
                    $url .= '?' . http_build_query($data);
                }
        }
        //var_dump($url,$data);die;
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($curl, CURLOPT_TIMEOUT, 15);

        $response = curl_exec($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $headerCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $responseBody = substr($response, $header_size);

        curl_close($curl);

       // if ($headerCode === 401 && $this->refreshToken === 0) {
       //     $retval = $this->sendRequest($path, $method, $data);
       // } else {
            $retval = new stdClass();
            $retval->data = json_decode($responseBody);
            $retval->http_code = $headerCode;
       // }

        return $retval;
    }

    /**
     * Process results
     *
     * @param $data
     *
     * @return stdClass
     */
    protected function handleResult($data)
    {
        if (empty($data->data)) {
            $data->data = new stdClass();
        }
        if ($data->http_code !== 200) {
            $data->data->is_error = true;
            $data->data->http_code = $data->http_code;
        }

        return $data->data;
    }

    /**
     * Process errors
     *
     * @param null $customMessage
     *
     * @return stdClass
     */
    protected function handleError($customMessage = null)
    {
        $message = new stdClass();
        $message->is_error = true;
        if (null !== $customMessage) {
            $message->message = $customMessage;
        }

        return $message;
    }


    /*
     * API interface implementation
     */


    /**
     * Create address book
     *
     * @param $bookName
     *
     * @return stdClass
     */
    public function orders($paymentCreditsId,
                          $email,
                          $phone,
                          $transactionDate,
                          $offerId,
                          $accountId,
                          $isNotifyAccount)
    {
        if (empty($accountId)) {
            return $this->handleError('Empty accountId');
        }

        $data = [];
        $data['paymentCreditsId']   = $paymentCreditsId;
        $data['email']              = $email;
        $data['phone']              = $phone;
        $data['transactionDate']    = $transactionDate;
        $data['offerId']            = $offerId;
        $data['accountId']          = $accountId;
        $data['isNotifyAccount']    = $isNotifyAccount;


        $requestResult = $this->sendRequest('orders', 'POST', $data);

        return $this->handleResult($requestResult);
    }


}
