# Craft reCAPTCHA plugin for Craft CMS 3.x

Integrate Google’s reCAPTCHA into your forms.
Includes support for the CraftCMS [Contact Form](https://github.com/craftcms/contact-form) plugin.

## Requirements

This plugin requires Craft CMS 3.1 or later.

**This plugin supports reCAPTCHA v2 only.**

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

Or alternatively, use the in-built verification controller action to verify the request before forwarding it on to the intended action.

For example, the following fields would verify the reCAPTCHA and then pass the request to the login controller action:

```twig
<input type="hidden" name="action" value="recaptcha/recaptcha/verify-submission">
<input type="hidden" name="verified-action" value="users/login">
{{ craft.recaptcha.render() }}
```

Set the `action` field to be `recaptcha/recaptcha/verify-submission` and the `verified-action` field to be the intended controller action you want to trigger. This will forward all other fields and parameters to the intended controller action.

### Automated testing and reCAPTCHA

If you need to run automated tests against your forms use the following keys. Verification requests using these credentials will always pass.

Site key: `6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI`
Secret key: `6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe`

[Documentation](https://developers.google.com/recaptcha/docs/faq#id-like-to-run-automated-tests-with-recaptcha-v2-what-should-i-do)

## Using Craft reCAPTCHA

Add the following tag to your form where you’d like the reCAPTCHA to be displayed.

```twig
{{ craft.recaptcha.render() }}
```

Render parameters [per the documentation](https://developers.google.com/recaptcha/docs/display#render_param) are injectable to the `render()` function, e.g.

```twig
{{ craft.recaptcha.render({
  theme: 'dark',
  size: 'compact'
}) }}
```

You can also create the reCAPTCHA element yourself using the `sitekey` template variable. This is especially useful for implementing invisible recaptcha.

```twig
<div class="g-recaptcha"
      data-sitekey="{{ craft.recaptcha.sitekey }}"
      data-callback="onSubmit"
      data-size="invisible">
</div>
```

---

Brought to you by [Matt West](https://mattwest.io)
