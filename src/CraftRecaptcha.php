<?php
/**
 * Craft reCAPTCHA plugin for Craft CMS 3.x
 *
 * Integrate Google’s reCAPTCHA into your forms.
 *
 * @link      https://mattwest.io
 * @copyright Copyright (c) 2018 Matt West
 */

namespace mattwest\craftrecaptcha;

use mattwest\craftrecaptcha\services\CraftRecaptchaService;
use mattwest\craftrecaptcha\variables\CraftRecaptchaVariable;
use mattwest\craftrecaptcha\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterUrlRulesEvent;
use craft\contactform\models\Submission;

use yii\base\Event;
use yii\base\ModelEvent;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://craftcms.com/docs/plugins/introduction
 *
 * @author    Matt West
 * @package   CraftRecaptcha
 * @since     1.0.0
 *
 * @property  CraftRecaptchaService $craftRecaptchaService
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class CraftRecaptcha extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * CraftRecaptcha::$plugin
     *
     * @var CraftRecaptcha
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * CraftRecaptcha::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Register our variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('recaptcha', CraftRecaptchaVariable::class);
            }
        );

        // Do something after we're installed
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    // We were just installed
                }
            }
        );

        // Set up contact form hook.
        $settings = CraftRecaptcha::$plugin->getSettings();

        if (class_exists(Submission::class) && $settings->validateContactForm) {
            Event::on(Submission::class, Submission::EVENT_BEFORE_VALIDATE, function(ModelEvent $e) {
                /** @var Submission $submission */
                $submission = $e->sender;

                $captcha = Craft::$app->getRequest()->getParam('g-recaptcha-response');

                $validates = CraftRecaptcha::$plugin->craftRecaptchaService->verify($captcha);

                if (!$validates) {
                    $submission->addError('recaptcha', 'Please verify you are human.');
                    $e->isValid = false;
                }
            });
        }

/**
 * Logging in Craft involves using one of the following methods:
 *
 * Craft::trace(): record a message to trace how a piece of code runs. This is mainly for development use.
 * Craft::info(): record a message that conveys some useful information.
 * Craft::warning(): record a warning message that indicates something unexpected has happened.
 * Craft::error(): record a fatal error that should be investigated as soon as possible.
 *
 * Unless `devMode` is on, only Craft::warning() & Craft::error() will log to `craft/storage/logs/web.log`
 *
 * It's recommended that you pass in the magic constant `__METHOD__` as the second parameter, which sets
 * the category to the method (prefixed with the fully qualified class name) where the constant appears.
 *
 * To enable the Yii debug toolbar, go to your user account in the AdminCP and check the
 * [] Show the debug toolbar on the front end & [] Show the debug toolbar on the Control Panel
 *
 * http://www.yiiframework.com/doc-2.0/guide-runtime-logging.html
 */
        Craft::info(
            Craft::t(
                'recaptcha',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * Returns the rendered settings HTML, which will be inserted into the content
     * block on the settings page.
     *
     * @return string The rendered settings HTML
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'recaptcha/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }
}
