<?php
/**
 * Модуль выводит список марок и моделей
 *
 * @param  JRegistry  $params  module params
 * @param  JSite      $app
 * @param  stdClass   $module  @see JModuleHelper::renderModule
 * @param  string     $option  com_content for example
 * @param  unknown    $scope
 * @param  string     $path    absolute path to current module file, like __FILE__
 * @param  JLanguage  $lang
 */

// no direct access
defined('_JEXEC') or die;

// проверка включенности компонента
if (!JComponentHelper::isEnabled('com_maxposter', $strict = true)) {
    if (defined('JDEBUG') && constant('JDEBUG')) {
        // Load error template
        require (JModuleHelper::getLayoutPath($module->module, $params->get('layout', 'default') . '_error'));
    }
    // just return silently
    return;
}
// JInput
$JInput = $app->input;

// Component settings
$maxParams = JComponentHelper::getParams('com_maxposter', $strict = true); # $app->getParams('com_maxposter')
$params->merge($maxParams);

// Search params if any
$search = $JInput->get(sprintf('%ssearch', $params->get('prefix', '')), array(), 'ARRAY');
$cleaner = create_function('$search, &$self', '$tmp = array();foreach($search as $k => $v){if(is_array($v)){
$res=$self($v,$self);if(count($res)){$tmp[$k]=$res;}
}elseif(!empty($v)){
$tmp[$k] = $v;
}};return $tmp;');
$search = $cleaner($search,$cleaner);

// MaxPoster libraries
jimport('maxposter.maxCacheHtmlClient');
require_once (JPATH_SITE.'/components/com_maxposter/lib/client/maxClient.php');
require_once (JPATH_SITE.'/components/com_maxposter/helpers/helper.php');

# Styles
# base
JHtml::stylesheet('maxposter/mod_maxposter_manufacturers.css', array(), true, false, false);
# client overrides
JHtml::stylesheet('mod_maxposter_manufacturers.css', array(), true);

# JS
if ($params->get('enable_js', false)) {
    # base
    JHtml::script('maxposter/mod_maxposter_manufacturers.js', false, true, false, false);
    # client overrides
    JHtml::script('mod_maxposter_manufacturers.js', false, true);
}

// Определение, какие данные необходимы от Интернет-сервиса
$client = new maxClient(MaxPosterHelper::getConfig());
$client->setRequestThemeName('marks');

// TODO: copypaste перенести кеширование xml в одно место
// кеширование XML
$cache = JFactory::getCache('com_maxposter', '');
$cache->setCaching(true);
$cache->setLifeTime(60);
$cache->_getStorage()->_lifetime = 60;
$cacheId = $client->getRequestCacheId();

if (!$rawXml = $cache->get($cacheId)) {
    $xml = $client->getXml();
    $responceId = $xml->getElementsByTagName('response')->item(0)->getAttribute('id');
    if ('error' != $responceId) {
        list($cacheActualAt, $cacheExpiresAt) = $client->getCacheTimes();
        $cacheLife = (int) $cacheExpiresAt - time(); # кешируем в секундах
        $cache->setLifeTime($cacheLife);
        $cache->_getStorage()->_lifetime = $cacheLife;
        if ($cacheLife > 1) {
            $cache->store($xml->saveXml(), $cacheId);
        }
    }
} else {
    $xml = new DOMDocument();
    $xml->loadXML($rawXml);
    $responceId = $xml->getElementsByTagName('response')->item(0)->getAttribute('id');
}

switch ($responceId) {
    # ошибка запроса, сервис недоступен и т.п.
    case 'error':
        $params->set('error', 404);
        $cache->setCaching(false);
        $cache->setLifeTime(0);
        $cache->_getStorage()->_lifetime = 0;
        # FIXME: сейчас в случае ошибки модуль не будет выведен
        return '';
        break;
}

# loading helper
require_once(dirname(__FILE__) . '/helper.php');
$helper = new modMaxPosterManufacturerHelper($xml, $params);
$helper->setSearch($search);

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

// Template
// TODO: кешировать
require (JModuleHelper::getLayoutPath($module->module, $params->get('layout', 'default')));
