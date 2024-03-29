<?php
/**
 * BuddyBoss Compatibility Integration Class.
 *
 * @since BuddyBoss 1.1.5
 */

// Exit if accessed directly.
defined('ABSPATH') || exit();

/**
 * Setup the bp compatibility class.
 *
 * @since BuddyBoss 1.1.5
 */
class PHOTOCAT_BuddyBoss_Integration extends BP_Integration
{
    public function __construct()
    {
        $this->start(
            'photo-cat',
            __('Photo Categorization', 'buddyboss-photo-categorization'),
            'photo-cat',
            [
                'required_plugin' => [],
            ]
        );

        // Add link to settings page.
        add_filter('plugin_action_links', [$this, 'action_links'], 10, 2);
        add_filter(
            'network_admin_plugin_action_links',
            [$this, 'action_links'],
            10,
            2
        );
    }

    /**
     * Register admin integration tab
     */
    public function setup_admin_integration_tab()
    {
        require_once 'buddyboss-addon-integration-tab.php';

        new PHOTOCAT_BuddyBoss_Admin_Integration_Tab(
            "bp-{$this->id}",
            $this->name,
            [
                'root_path' => PHOTOCAT_BB_ADDON_PLUGIN_PATH . '/integration',
                'root_url' => PHOTOCAT_BB_ADDON_PLUGIN_URL . '/integration',
                'required_plugin' => $this->required_plugin,
            ]
        );
    }

    public function action_links($links, $file)
    {
        // Return normal links if not BuddyPress.
        if (PHOTOCAT_BB_ADDON_PLUGIN_BASENAME != $file) {
            return $links;
        }

        // Add a few links to the existing links array.
        return array_merge($links, [
            'settings' =>
                '<a href="' .
                esc_url(
                    bp_get_admin_url(
                        'admin.php?page=bp-integrations&tab=bp-photo-cat'
                    )
                ) .
                '">' .
                __('Settings', 'buddyboss-photo-categorization') .
                '</a>',
        ]);
    }
}
