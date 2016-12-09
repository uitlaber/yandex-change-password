<?php

class YandexChangePasswordPlugin extends \RainLoop\Plugins\AbstractPlugin
{
    public function Init()
    {
        $this->addHook('main.fabrica', 'MainFabrica');
    }

    /**
     * @param string $sName
     * @param mixed $oProvider
     */
    public function MainFabrica($sName, &$oProvider)
    {
        switch ($sName) {
            case 'change-password':

                include_once __DIR__ . '/ChangePasswordYandexDriver.php';

                $oProvider = new ChangePasswordYandexDriver();

                $oProvider->SetToken($this->Config()->Get('plugin', 'pdd_token', ''));
                $oProvider->SetHost($this->Config()->Get('plugin', 'host', ''));
                $oProvider->SetUrl($this->Config()->Get('plugin', 'url', ''));
                $oProvider->SetDomain($this->Config()->Get('plugin', 'domain', ''));
                $oProvider->SetAllowedEmails(\strtolower(\trim($this->Config()->Get('plugin', 'allowed_emails', ''))));

                break;
        }
    }

    /**
     * @return array
     */
    public function configMapping()
    {
        return array(
            \RainLoop\Plugins\Property::NewInstance('host')->SetLabel('yandex domain host')
                ->SetDefaultValue('pddimp.yandex.ru'),
            \RainLoop\Plugins\Property::NewInstance('pdd_token')->SetLabel('PddToken')
                ->SetDefaultValue(''),
            \RainLoop\Plugins\Property::NewInstance('url')->SetLabel('Url')
                ->SetDefaultValue('/api2/admin/email/edit'),
            \RainLoop\Plugins\Property::NewInstance('domain')->SetLabel('Domain')
                ->SetDefaultValue(''),
            \RainLoop\Plugins\Property::NewInstance('allowed_emails')->SetLabel('Allowed emails')
                ->SetType(\RainLoop\Enumerations\PluginPropertyType::STRING_TEXT)
                ->SetDescription('Allowed emails, space as delimiter, wildcard supported. Example: user1@domain1.net user2@domain1.net *@domain2.net')
                ->SetDefaultValue('*')
        );
    }
}
