<?php
/**
 * @package      Crowdfunding
 * @subpackage   Modules
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined("_JEXEC") or die;

jimport('Prism.init');
jimport('Crowdfunding.init');
JLoader::register('CrowdfundingPopularModuleHelper', JPATH_ROOT . '/modules/mod_crowdfundingpopular/helper.php');

$moduleclassSfx = htmlspecialchars($params->get('moduleclass_sfx'));

$limitResults = $params->get('results_limit', 5);
if ($limitResults <= 0) {
    $limitResults = 5;
}

$projects = CrowdfundingPopularModuleHelper::getProjects($limitResults);

// Get component parameters
$componentParams = JComponentHelper::getParams('com_crowdfunding');
/** @var  $componentParams Joomla\Registry\Registry */

// Get options
$displayInfo        = $params->get('show_info', Prism\Constants::DISPLAY);
$displayDescription = $params->get('show_description', $componentParams->get('show_description', Prism\Constants::DISPLAY));
$displayCreator     = $params->get('show_author', $componentParams->get('show_author', Prism\Constants::DISPLAY));
$displayReadon      = $params->get('show_readon', Prism\Constants::DO_NOT_DISPLAY);
$displaySeeProjects = $params->get('show_see_projects', Prism\Constants::DO_NOT_DISPLAY);
$titleLength        = $params->get('title_length', $componentParams->get('title_length'));
$descriptionLength  = $params->get('description_length', $componentParams->get('description_length'));

$imagesDirectory = $componentParams->get('images_directory', 'images/crowdfunding');
$dateFormat      = $componentParams->get('date_format_views', JText::_('DATE_FORMAT_LC3'));

if ($displayInfo) {
    $container       = Prism\Container::getContainer();
    /** @var  $container Joomla\DI\Container */

    $containerHelper = new Crowdfunding\Container\Helper();
    $money           = $containerHelper->fetchMoneyFormatter($container, $componentParams);
}

// Display user social profile ( integrate ).
if ($displayCreator) {
    $socialProfiles = null;

    // Get a social platform for integration
    $socialPlatform = $componentParams->get('integration_social_platform');
    if ($socialPlatform !== null and $socialPlatform !== '') {
        $usersIds       = Prism\Utilities\ArrayHelper::getIds($projects, 'user_id');
        $socialProfiles = CrowdfundingHelper::prepareIntegration($socialPlatform, $usersIds);
    }
}

if (count($projects) > 0) {
    require JModuleHelper::getLayoutPath('mod_crowdfundingpopular', $params->get('layout', 'default'));
}
