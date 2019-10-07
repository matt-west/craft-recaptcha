<?php
/**
 * Craft reCAPTCHA plugin for Craft CMS 3.x
 *
 * Integrate Google’s reCAPTCHA into your forms.
 *
 * @link      https://mattwest.io
 * @copyright Copyright (c) 2018 Matt West
 */

namespace mattwest\craftrecaptcha\models;

use mattwest\craftrecaptcha\CraftRecaptcha;

use Craft;
use craft\base\Model;
use craft\behaviors\EnvAttributeParserBehavior;

/**
 * CraftRecaptcha Settings Model
 *
 * This is a model used to define the plugin's settings.
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Matt West
 * @package   CraftRecaptcha
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * Site key model attribute
     *
     * @var string
     */
    public $siteKey = '';

    /**
     * Secret key model attribute
     *
     * @var string
     */
    public $secretKey = '';

    /**
     * Validate ContactForm submissions
     *
     * @var bool
     */
    public $validateContactForm = true;



    // Public Methods
    // =========================================================================

  /**
   * @return string the parsed site key (e.g. 'XXXXXXXXXXX')
   */
  public function getSiteKey(): string
  {
    return Craft::parseEnv($this->siteKey);
  }

  /**
   * @return string the parsed secret key (e.g. 'XXXXXXXXXXX')
   */
  public function getSecretKey(): string
  {
    return Craft::parseEnv($this->secretKey);
  }

  public function behaviors()
  {
    return [
        'parser' => [
            'class' => EnvAttributeParserBehavior::class,
            'attributes' => ['siteKey', 'secretKey'],
        ],
    ];
  }

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        return [
            ['siteKey', 'string'],
            ['secretKey', 'string'],
            [['siteKey', 'secretKey'], 'required']
        ];
    }
}
