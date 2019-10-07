<?php
/**
 * Craft reCAPTCHA plugin for Craft CMS 3.x
 *
 * Integrate Google’s reCAPTCHA into your forms.
 *
 * @link      https://mattwest.io
 * @copyright Copyright (c) 2018 Matt West
 */

namespace mattwest\craftrecaptcha\variables;

use mattwest\craftrecaptcha\CraftRecaptcha;

use Craft;

/**
 * Craft reCAPTCHA Variable
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.craftRecaptcha }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    Matt West
 * @package   CraftRecaptcha
 * @since     1.0.0
 */
class CraftRecaptchaVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Render the reCAPTCHA widget.
     *
     *    {{ craft.recaptcha.render() }}
     *
     * @param array $options
     * @return string
     */
    public function render(array $options = [])
    {
      return CraftRecaptcha::$plugin->craftRecaptchaService->render($options);
    }

    public function sitekey()
    {
      $settings = CraftRecaptcha::$plugin->getSettings();

      return $settings->getSiteKey();
    }
}
