<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Script file of Maxposter component
 */
class mod_maxposter_manufacturersInstallerScript
{
    /**
     * method to install the component
     *
     * @return void
     */
    function install($parent)
    {
        // $parent is the class calling this method
        // can be redirected to installed component
        $parent->getParent()->setRedirectURL('index.php?option=com_modules');
    }


    /**
     * method to uninstall the component
     *
     * @return void
     */
    function uninstall($parent)
    {
        // $parent is the class calling this method
        // echo '<p>' . JText::_('COM_HELLOWORLD_UNINSTALL_TEXT') . '</p>';
    }


    /**
     * method to update the component
     *
     * @return void
     */
    function update($parent)
    {
        // $parent is the class calling this method
        // echo '<p>' . JText::_('COM_HELLOWORLD_UPDATE_TEXT') . '</p>';
    }


    /**
     * method to run before an install/update/uninstall method
     *
     * @return void
     */
    function preflight($type, $parent)
    {
        // $parent is the class calling this method
        // $type is the type of change (install, update or discover_install)
        // echo '<p>' . JText::_('COM_HELLOWORLD_PREFLIGHT_' . $type . '_TEXT') . '</p>';
    }


    /**
     * method to run after an install/update/uninstall method
     *
     * @return void
     */
    function postflight($type, $parent)
    {
        // $parent is the class calling this method
        // $type is the type of change (install, update or discover_install)
        // echo '<p>' . JText::_('COM_HELLOWORLD_POSTFLIGHT_' . $type . '_TEXT') . '</p>';
        // Clean media directory, if old files is still there
        // TODO: detect installed version in preflight
        if ('Update' == $type) {
            if (JFile::exists(JPATH_ROOT . '/media/maxposter/css/mod_maxposter_manufacturers.css')) {
                JFile::delete(JPATH_ROOT . '/media/maxposter/css/mod_maxposter_manufacturers.css');
            }
            if (JFile::exists(JPATH_ROOT . '/media/maxposter/js/mod_maxposter_manufacturers.js')) {
                JFile::delete(JPATH_ROOT . '/media/maxposter/js/mod_maxposter_manufacturers.js');
            }
            if (JFolder::exists(JPATH_ROOT . '/media/maxposter/images/mod_maxposter_manufacturers')) {
                JFolder::delete(JPATH_ROOT . '/media/maxposter/images/mod_maxposter_manufacturers');
            }
        }
    }

}
