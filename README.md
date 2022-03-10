# Craft reCAPTCHA plugin for Craft CMS 3.x

Integrate Google’s reCAPTCHA into your forms.
Includes support for the CraftCMS [Contact Form](https://github.com/craftcms/contact-form) plugin.

## Requirements

This plugin requires Craft CMS 3.1 or later.

**This plugin now supports reCAPTCHA v3 while still using v2 as a fallback!**

## Installation

Install through the Craft plugin store.

Alternatively, to install the plugin through your CLI, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require matt-west/craft-recaptcha

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Craft reCAPTCHA.

## Configuring Craft reCAPTCHA

1. [Sign up for a v2 and v3 reCAPTCHA API key](https://www.google.com/recaptcha/admin).
2. Open the Craft admin and go to Settings → Plugins → Craft reCAPTCHA → Settings.
3. Add your site keys and secret keys, then save.
4. Add the reCAPTCHA template tag to your forms. (see next section)

If you’re using the CraftCMS [Contact Form](https://github.com/craftcms/contact-form) plugin, verification is done automatically for you.

### Verify the reCAPTCHA

To verify the reCAPTCHA is valid, check for either the reCAPTCHA response from the `recaptcha_response` (v3) or `g-recaptcha-response` (v2) param to the `verify()` method on `CraftRecaptcha::$plugin->craftRecaptchaService`.

```php
use Craft;

// See if a v3 response code is present
$version = 3;
$captcha = Craft::$app->getRequest()->getParam('recaptcha_response');
if ($captcha === null) {
  //if no v3 param exists, try v2
  $version = 2;
  $captcha = Craft::$app->getRequest()->getParam('g-recaptcha-response');
}

// Pass the response code to the verification service.
$validates = CraftRecaptcha::$plugin->craftRecaptchaService->verify($captcha, $version, 'my-action', 0.5);

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
<input type="hidden" name="recaptcha-action" value="my-action">
<input type="hidden" name="recaptcha-threshold" value="0.5">
{{ craft.recaptcha.render({
  recaptchaAction: 'my-action'
}) }}
```

Set the `action` field to be `recaptcha/recaptcha/verify-submission` and the `verified-action` field to be the intended controller action you want to trigger. This will forward all other fields and parameters to the intended controller action.

### Automated testing and reCAPTCHA

If you need to run automated tests against your forms use the following keys. Verification requests using these credentials will always pass.

Site key: `6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI`
Secret key: `6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe`

[Documentation](https://developers.google.com/recaptcha/docs/faq#id-like-to-run-automated-tests-with-recaptcha.-what-should-i-do)

## Using Craft reCAPTCHA

Add the following tag to your form where you’d like the reCAPTCHA to be displayed.

```twig
{{ craft.recaptcha.render() }}
```

The tag will automatically use a v3 reCAPTCHA on the first page load. If the verification fails, a v2 reCAPTCHA will be loaded instead.

Render parameters [per the documentation](https://developers.google.com/recaptcha/docs/display#render_param) are injectable to the `render()` function, e.g.

```twig
{{ craft.recaptcha.render({
  theme: 'dark',
  size: 'compact'
}) }}
```

You can also create the reCAPTCHA element yourself using the `sitekey` and `sitekeyV3` template variables and the `recaptchaValid` param. This is especially useful for implementing invisible recaptcha.

```twig
{% if recaptchaValid is defined and recaptchaValid == false %}
  {# a failed attempt has been made, use v2 #}
  <div class="g-recaptcha" id="g-recaptcha-v2" data-size="invisible"></div>
  {% js %}
    var onloadCallback = function() {
      grecaptcha.render(
        document.getElementById('g-recaptcha-v2'),
        {"sitekey": "{{ craft.recaptcha.sitekey }}", "theme": "dark"}
      );
    };
  {% endjs  %}
  {% do view.registerJsFile("https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit") %}
{% else %}
  {# no verification attempts yet, so use v3 #}
  <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
  {% js %}
    grecaptcha.ready(function() {
      grecaptcha.execute('{{ craft.recaptcha.sitekeyV3 }}', {action: 'contact'}).then(function(token) {
        var recaptchaResponse = document.getElementById('recaptchaResponse');
        recaptchaResponse.value = token;
      });
    });
  {% endjs %}
{% endif %}
```

---

Brought to you by [Matt West](https://mattwest.io)
