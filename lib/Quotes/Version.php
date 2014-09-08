<?php
/**
 * Zikula Application Framework
 * @copyright  (c) Zikula Development Team
 * @license    GNU/GPL
 * @category   Zikula_3rdParty_Modules
 * @package    Content_Management
 * @subpackage Quotes
 */
class Quotes_Version extends Zikula_AbstractVersion
{
    public function getMetaData() {
        $meta = array();
        $meta['displayname'] = $this->__('Quotes publisher');
        $meta['description'] = $this->__('Manage and display quotes or reflections, with support for categories.');
        $meta['version'] = '3.1.1';
        $meta['url'] = $this->__('quotes');
        $meta['core_min'] = '1.3.0'; // requires minimum 1.3.0 or later
        $meta['capabilities']   = array(HookUtil::SUBSCRIBER_CAPABLE => array('enabled' => true),
                                        HookUtil::PROVIDER_CAPABLE => array('enabled' => true));
        $meta['securityschema'] = array('Quotes::' => 'Author name::Quote ID');
        return $meta;
    }

    protected function setupHookBundles()
    {
        // Register hooks
        $bundle = new Zikula_HookManager_SubscriberBundle($this->name, 'subscriber.quotes.ui_hooks.items', 'ui_hooks', $this->__('Quotes Items Hooks'));
        $bundle->addEvent('display_view', 'quotes.ui_hooks.items.display_view');
        $bundle->addEvent('form_edit', 'quotes.ui_hooks.items.form_edit');
        $this->registerHookSubscriberBundle($bundle);

        $bundle = new Zikula_HookManager_ProviderBundle($this->name, 'provider.quotes.ui_hooks.quote', 'ui_hooks', $this->__('Quote Item'));
        $bundle->addServiceHandler('display_view', 'Quotes_HookHandlers', 'uiView', 'quotes.quote');
        $this->registerHookProviderBundle($bundle);
    }
}
