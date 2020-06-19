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
		
		// grab the recaptcha response (required)
		$captcha = $request->getRequiredParam('g-recaptcha-response');
		
		// run these past the verify() function
		$verified = CraftRecaptcha::$plugin->craftRecaptchaService->verify($captcha);
		
		// if it's verified, then pass it on to the intended action, otherwise set a session error and return null
		if ($verified) {
			return Controller::run('/' . $action, func_get_args()); // run the intended action (add / to force it's scope to be outside the plugin) with all the params passed to this controller action
		} else {
			Craft::$app->getSession()->setError('Unable to verify your submission.');
			return null;
		}
	}
}
