<?php
namespace app\components;

class HttpHeaders extends \yii\base\Component{
	public function init(){
		$headers = \Yii::$app->response->headers;
		$headers->set('X-Frame-Options', 'SAMEORIGIN');
		$headers->set('X-XSS-Protection', '1; mode=block');
		$headers->set('X-Content-Type-Options', 'nosniff');
		$headers->set('cache-control', 'no-cache, no-store, must-revalidate, private, post-check=0, pre-check=0');
		parent::init();
	}	
}