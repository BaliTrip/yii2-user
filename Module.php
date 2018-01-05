<?php
/**
 * @link https://github.com/balitrip/yii2-user
 * @copyright Copyright (c) 2014 Evgeny Zakirov
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace balitrip\user;

use Yii;
use yii\base\InvalidConfigException;


class Module extends \yii\base\Module
{
	public $controllerNamespace = 'balitrip\user\controllers';
	public $profileModel = '\balitrip\user\models\Profile';
	public $layout = 'clean';

	public function init()
    {
        parent::init();

        $this->registerTranslations();
    }

    public function registerTranslations()
	{
	    Yii::$app->i18n->translations['user'] = [
	        'class' => 'yii\i18n\PhpMessageSource',
	        'sourceLanguage' => 'en-EN',
	        'basePath' => '@balitrip/user/messages',
	    ];
	}

	public static function t($category, $message, $params = [], $language = null)
	{
	    return Yii::t('modules/user/' . $category, $message, $params, $language);
	}
}