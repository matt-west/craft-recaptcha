<?php
/**
 * Craft reCAPTCHA plugin for Craft CMS 3.x
 *
 * Integrate Google’s reCAPTCHA into your forms.
 *
 * @link      https://mattwest.io
 * @copyright Copyright (c) 2018 Matt West
 */

namespace mattwest\craftrecaptcha\services;

use mattwest\craftrecaptcha\CraftRecaptcha;

use Craft;
use craft\base\Component;
use craft\web\View;
use GuzzleHttp;

/**
 * CraftRecaptchaService Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Matt West
 * @package   CraftRecaptcha
 * @since     1.0.0
 */
class CraftRecaptchaService extends Component
{
    // Public Methods
    // =========================================================================
    
    public function render(array $options = [])
    {
        $routeParams = Craft::$app->getUrlManager()->getRouteParams();
        $settings = CraftRecaptcha::$plugin->getSettings();
        $currentLanguage = (Craft::$app->getSites()->getCurrentSite()->language) ? Craft::$app->getSites()->getCurrentSite()->language :'en' ;
        
        if($settings->getSiteKeyV3() && !isset($routeParams['variables']['recaptchaValid'])){
            Craft::$app->view->registerJsFile('https://www.google.com/recaptcha/api.js?render='. $settings->getSiteKeyV3() .'&hl='. $currentLanguage);
        }
        else{
            Craft::$app->view->registerJsFile('https://www.google.com/recaptcha/api.js?&hl='. $currentLanguage);
        }
        
        $defaultOptions = [
            'siteKey' => $settings->getSiteKey(),
            'siteKeyV3' => $settings->getSiteKeyV3(),
            'recaptchaAction' => 'contact'
        ];
        
        $vars = array(
            'id' => 'gRecaptchaContainer',
            'options' => array_merge($defaultOptions, $options)
        );
        if(isset($routeParams['variables']['recaptchaValid'])) $vars['recaptchaValid'] = $routeParams['variables']['recaptchaValid'];
        
        $oldMode = Craft::$app->view->getTemplateMode();
        Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_CP);
        
        $html = Craft::$app->view->renderTemplate('recaptcha/_recaptcha', $vars);
        
        Craft::$app->view->setTemplateMode($oldMode);
        
        echo $html;
    }
    
    /**
     * @param string $data
     * @param integer $version Either 2 or 3
     * @param string $action See https://developers.google.com/recaptcha/docs/v3#actions
     * @param float|string $threshold See https://developers.google.com/recaptcha/docs/v3#interpreting_the_score
     * @return bool
     * @throws GuzzleHttp\Exception\GuzzleException
     */
    public function verify(string $data, int $version, string $action = '', $threshold = 0.5)
    {
        $base = "https://www.google.com/recaptcha/api/siteverify";
        
        $settings = CraftRecaptcha::$plugin->getSettings();
        
        $secret = $version === 3 ? $settings->getSecretKeyV3() : $settings->getSecretKey();
        
        $params = array(
            'secret' =>  $secret,
            'response' => $data
        );
        
        if ($settings->shareUserIPs) {
            $ip = Craft::$app->getRequest()->userIP;
            
            if ($ip) {
                $params['remoteip'] = $ip;
            }
        }
        
        $client = new GuzzleHttp\Client();
        
        $response = $client->request('POST', $base, ['form_params' => $params]);
        $valid = false;
        if($response->getStatusCode() == 200)
        {
            $json = json_decode($response->getBody());
            
            if($json->success)
            {
                // Take action based on the score returned if v3
                if($version === 3){
                    if(($action ? $json->action === $action : true) && 0.5 >= floatval($threshold)) {
                        $valid = true;
                    }
                }
                else $valid = true;
            }
        }
        Craft::$app->getUrlManager()->setRouteParams([
            'variables' => ['recaptchaValid' => $valid]
        ]);
        return $valid;
    }
}
