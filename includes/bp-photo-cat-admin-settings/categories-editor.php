<?php
defined('ABSPATH') || exit();

if (!function_exists('PHOTOCAT_get_settings_sections')) {
    function PHOTOCAT_get_settings_sections()
    {
        $settings = [
            'PHOTOCAT_settings_section' => [
                'page' => 'addon',
                'title' => __(
                    'Photo Categorization',
                    'buddyboss-photo-categorization'
                ),
            ],
        ];

        return (array) apply_filters(
            'PHOTOCAT_get_settings_sections',
            $settings
        );
    }
}

if (!function_exists('PHOTOCAT_get_settings_fields_for_section')) {
    function PHOTOCAT_get_settings_fields_for_section($section_id = '')
    {
        // Bail if section is empty
        if (empty($section_id)) {
            return false;
        }

        $fields = PHOTOCAT_get_settings_fields();
        $retval = isset($fields[$section_id]) ? $fields[$section_id] : false;

        return (array) apply_filters(
            'PHOTOCAT_get_settings_fields_for_section',
            $retval,
            $section_id
        );
    }
}

if (!function_exists('PHOTOCAT_get_settings_fields')) {
    function PHOTOCAT_get_settings_fields()
    {
        $fields = [];

        $fields['PHOTOCAT_settings_section'] = [
            'PHOTOCAT_cat_number' => [
                'title' => __(
                    'Category count',
                    'buddyboss-photo-categorization'
                ),
                'callback' => 'PHOTOCAT_settings_callback_category_count',
                'sanitize_callback' => 'absint',
                'args' => [],
            ],
        ];

        $cat_number = intval(get_option('PHOTOCAT_cat_number', "1"));

        // TODO: definitely not the best way to store the settings, but will work
        // well enough for the proof of concept draft.
        for ($i = 1; $i <= $cat_number; $i++) {
            $fields['PHOTOCAT_settings_section']["PHOTOCAT_categories_$i"] = [
                'title' =>
                    "$i. " . __("Label", 'buddyboss-photo-categorization'),
                'callback' => 'PHOTOCAT_settings_callback_categories',
                'sanitize_callback' => 'text',
                'args' => $i,
            ];
            $fields['PHOTOCAT_settings_section'][
                "PHOTOCAT_category_options_$i"
            ] = [
                'title' =>
                    "$i. " . __("Options", 'buddyboss-photo-categorization'),
                'callback' => 'PHOTOCAT_settings_callback_category_options',
                'sanitize_callback' => 'text',
                'args' => $i,
            ];
        }

        return (array) apply_filters('PHOTOCAT_get_settings_fields', $fields);
    }
}

if (!function_exists('PHOTOCAT_settings_callback_category_count')) {
    function PHOTOCAT_settings_callback_category_count()
    {
        ?>
        <input name="PHOTOCAT_cat_number"
               id="PHOTOCAT_cat_number"
               type="number"
               min="1"
               max="10"
               value="<?php echo get_option('PHOTOCAT_cat_number', "1"); ?>"
        />
        <label for="PHOTOCAT_cat_number"> &nbsp;(1 - 10) </label>
    <?php
    }
}

if (!function_exists('PHOTOCAT_settings_callback_categories')) {
    function PHOTOCAT_settings_callback_categories($i)
    {
        $id = "PHOTOCAT_categories_$i";
        $name = "PHOTOCAT_categories_$i";
        $category = get_option("PHOTOCAT_categories_$i", '');
        ?>
        <input
          id='<?= $id ?>'
          name='<?= $name ?>'
          placeholder='<?= __(
              'Category name',
              'buddyboss-photo-categorization'
          ) ?>''
          type='text'
          value='<?= $category ?>'
          />
        <?php
    }
}

if (!function_exists('PHOTOCAT_settings_callback_category_options')) {
    function PHOTOCAT_settings_callback_category_options($i)
    {
        $id = "PHOTOCAT_category_options_$i";
        $name = "PHOTOCAT_category_options_$i";
        $value = get_option("PHOTOCAT_category_options_$i", '');
        ?>
        <input
            id='<?= $id ?>'
            name='<?= $name ?>'
            placeholder='option1; option2; ...'
            type='text'
            size='40'
            value='<?= $value ?>'
        />
        <?php
    }
}
?>
