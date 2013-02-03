<?php
/**
 * Zikula Application Framework
 * @copyright  (c) Zikula Development Team
 * @license    GNU/GPL
 * @category   Zikula_3rdParty_Modules
 * @package    Content_Management
 * @subpackage Quotes
 */

/**
 * Quotes Hooks Handlers.
 */
class Quotes_HookHandlers extends Zikula_Hook_AbstractHandler
{

    /**
     * Display hook for view.
     *
     * @param Zikula_Hook $hook The hook.
     *
     * @return void
     */
    public function uiView(Zikula_DisplayHook $hook)
    {
        // Input from the hook
        $callermodname = $hook->getCaller();
        $callerobjectid = $hook->getId();

        // Check permissions
        if (!SecurityUtil::checkPermission('Quotes::', "::", ACCESS_READ)) {
            return;
        }

        // Get item
        $item = ModUtil::apiFunc('Quotes', 'user', 'getrandom', $args);

        // create the output object
        $view = Zikula_View::getInstance('Quotes', false, null, true);
        $view->assign('areaid', $hook->getAreaId());
        $view->assign('quote', $item);
        $view->assign('ballooncolor', 'grey');
        $template = 'quotes_user_display.tpl';

        // Add style
        PageUtil::addVar('stylesheet', 'modules/Quotes/style/style.css');

        $response = new Zikula_Response_DisplayHook('provider.quotes.ui_hooks.quote', $view, $template);
        $hook->setResponse($response);
    }

}
