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
        $settings = CraftRecaptcha::$plugin->getSettings();

        \Craft::$app->view->registerJsFile('https://www.google.com/recaptcha/api.js');

        $defaultOptions = [
            'siteKey' => $settings->getSiteKey()
        ];

        $vars = array(
            'id' => 'gRecaptchaContainer',
            'options' => array_merge($defaultOptions, $options)
        );

        $oldMode = \Craft::$app->view->getTemplateMode();
        \Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_CP);

        $html = \Craft::$app->view->renderTemplate('recaptcha/_recaptcha', $vars);

        \Craft::$app->view->setTemplateMode($oldMode);

        echo $html;
    }

    public function verify($data)
    {
      $base = "https://www.google.com/recaptcha/api/siteverify";

      $settings = CraftRecaptcha::$plugin->getSettings();
      $params = array(
          'secret' =>  $settings->getSecretKey(),
          'response' => $data
      );

      $client = new GuzzleHttp\Client();
      $response = $client->request('POST', $base, ['form_params' => $params]);

      if($response->getStatusCode() == 200)
      {
          $json = json_decode($response->getBody());

          if($json->success)
          {
              return true;
          } else {
              return false;
          }
      } else {
          return false;
      }
    }
}
