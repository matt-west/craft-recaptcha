<?php

namespace mattwest\craftrecaptcha\controllers;

use mattwest\craftrecaptcha\CraftRecaptcha;

use Craft;
use craft\web\Controller;
use yii\web\Response;

class RecaptchaController extends Controller
{
    protected $allowAnonymous = true;
    
    /**
     * Handle verifying the submission and then pass it on to the relevant action (or not).
     */
    public function actionVerifySubmission()
    {
        // ensure the request is a post
        $this->requirePostRequest();
        
        // grab the request object
        $request = Craft::$app->getRequest();
        
        // grab the intended action (required)
        $action = $request->getRequiredParam('verified-action');
        
        // grab the reCaptcha action
        $reCaptchaAction = $request->getParam('recaptcha-action');
        
        // grab the reCaptcha score threshold
        $threshold = $request->getParam('recaptcha-threshold');
        
        //Verify reCaptcha fields, first trying V3
        $version = 3;
        $captcha = Craft::$app->getRequest()->getParam('recaptcha_response');
        if ($captcha === null) {
            //no V3 param exists, try V2...
            $version = 2;
            $captcha = Craft::$app->getRequest()->getRequiredParam('g-recaptcha-response');
        }
        
        // run these past the verify() function
        $verified = CraftRecaptcha::$plugin->craftRecaptchaService->verify($captcha, $version, $reCaptchaAction, $threshold);
        
        // if it's verified, then pass it on to the intended action, otherwise set a session error and return null
        if ($verified) {
            return Controller::run('/' . $action, func_get_args()); // run the intended action (add / to force it's scope to be outside the plugin) with all the params passed to this controller action
        } else {
            Craft::$app->getSession()->setError(Craft::t('site', 'Unable to verify your submission.'));
            return null;
        }
    }
}
