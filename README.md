# Craft reCAPTCHA plugin for Craft CMS 3.x

Integrate Google’s reCAPTCHA into your forms.  
Includes support for the CraftCMS [Contact Form](https://github.com/craftcms/contact-form) plugin.

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require matt-west/craft-recaptcha

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Craft reCAPTCHA.

## Configuring Craft reCAPTCHA

1. [Sign up for reCAPTCHA API key](https://www.google.com/recaptcha/admin).
2. Open the Craft admin and go to Settings → Plugins → Craft reCAPTCHA → Settings.
3. Add your `site key` and `secret key`, then save.
4. Add the reCAPTCHA template tag to your forms. (see next section)

If you’re using the CraftCMS [Contact Form](https://github.com/craftcms/contact-form) plugin, everything is already set up for you.

### Verify the reCAPTCHA

To verify the reCAPTCHA is valid, pass the reCAPTCHA response from the `g-recaptcha-response` param to the `verify()` method on `CraftRecaptcha::$plugin->craftRecaptchaService`.

```php
// Get the reCAPTCHA response code to validate.
$captcha = Craft::$app->getRequest()->getParam('g-recaptcha-response');

// Pass the response code to the verification service.
$validates = CraftRecaptcha::$plugin->craftRecaptchaService->verify($captcha);

if ($validates) {
  // All good! the reCAPTCHA is valid.
} else {
  // The reCAPTCHA is invalid.
}
```

## Using Craft reCAPTCHA

Add the following tag to your form where you’d like the reCAPTCHA to be displayed.

```twig
{{ craft.recaptcha.render() }}
```

Brought to you by [Matt West](https://mattwest.io)