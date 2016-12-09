<?php

class ChangePasswordYandexDriver implements \RainLoop\Providers\ChangePassword\ChangePasswordInterface
{

    private $pdd_token;
    private $host;
    private $url;
    private $domain;
    private $sAllowedEmails = '';


    public function SetToken($token)
    {
        $this->pdd_token = $token;
    }

    public function SetHost($host)
    {
        $this->host = $host;
    }

    public function SetUrl($url)
    {
        $this->url = $url;
    }

    public function SetDomain($domain)
    {
        $this->domain = $domain;
    }

    public function SetAllowedEmails($sAllowedEmails)
    {
        $this->sAllowedEmails = $sAllowedEmails;
        return $this;
    }

    /**
     * @param \RainLoop\Account $oAccount
     *
     * @return bool
     */
    public function PasswordChangePossibility($oAccount)
    {
        return $oAccount && $oAccount->Email() &&
            \RainLoop\Plugins\Helper::ValidateWildcardValues($oAccount->Email(), $this->sAllowedEmails);
    }

    /**
     * @param \RainLoop\Account $oAccount
     * @param string $sPrevPassword
     * @param string $sNewPassword
     *
     * @return bool
     */
    public function ChangePassword(\RainLoop\Account $oAccount, $sPrevPassword, $sNewPassword)
    {
        $bResult = false;
        $data = array(
            'domain' => $this->domain,
            'login' => $oAccount->Email(),
            'password' => $sNewPassword
        );

        try {
            $url = 'https://'.$this->host . $this->url;
            $header = array(
                'PddToken: ' . $this->pdd_token,
                'Host: ' . $this->host
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_ENCODING, "gzip");
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

            $response = curl_exec($ch);

            curl_close($ch);

            $result = json_decode($response,true);
            if ($result['success'] == 'ok') {
                $bResult = true;
            } else {
                $bResult = false;
            }

        } catch (\Exception $oException) {
            $bResult = false;
        }

        return $bResult;
    }

}