## BuddyBoss - Photo categorization

Plugin commissioned by [Ebenup](https://www.ebenup.com/).

A Wordpress plugin integrating with [BuddyBoss](https://www.buddyboss.com/),
intended to allow users to categorize uploaded photos, based on categories
presets defined by admins.

The intent is to emulate a simple version of Pinterest collections.

---


### Features:
- [x] Categories selectors on photo upload, integrated in BuddyBoss.
- [x] Categories editor, in BuddyBoss's admin panel.
- [x] Default photo browsing, with category filters *(Wordpress short-code)*.
- [x] Pinterest-like `'Save picture'` widget, on the photo display page.
- [x] Default photo collections display page *(Wordpress short-code)*.

---
### Todo / caveats:
- Currently no way to configure which category must have a value selected on photo upload. Instead, the form only checks that the user selected at least one category.
- Changing a category option's name will break the already existing associations for photos.
- Currently no way to remove a photo from a collection, or add a photo to more than one collection.
- [Automatic] css/js minimization must be set up.
---

### Install:

- [Download this repos as zip](https://github.com/alexangc/buddyboss-photo-categorization/archive/master.zip).
- In Wordpress: `Extensions > Add > Upload an extension`, and upload the zip. Don't forget to activate the plugin.

---

### Uninstall:

- Disable and remove the extension from the Wordpress admin panel.
- Drop the following tables from your database manually and in this order *(add your custom prefixes if you have any)*:
  - `bp_photos_collections_items_categories`
  - `bp_photos_collections_items`
  - `bp_photos_collections`
  - `bp_photos_categories`

---

### Configuration:

- Admin settings:

The categories can be set from BuddyBoss's admin panel, at the following URL: `/wp-admin/admin.php?page=bp-integrations&tab=bp-photo-cat`. 'Labels' are the themes under which the categories will be displayed. If a label is empty, the category options will be ignored. If you want categories to be displayed without using the label, you can enter a space character as value for its 'label'.

Because this task is performed by admins, **no input control** has been implemented currently.


- Photo gallery:

The photo gallery page can be called through a [Wordpress shortcode](https://wordpress.com/support/shortcodes/). Create a page and add the following as content:
```
[PHOTOCAT_gallery_shortcode]
```

**Note**: when deleted by their uploaders, the tags and collection associations will also be deleted. This means that when an uploader deletes one of their photos, this photo disappears from everyone else's collections.

- Collection gallery:

Likewise, the collection gallery can be called through:

```
[PHOTOCAT_collections_shortcode]
```

**Note**: Although there is no direct way to delete a collection once created, empty collections will be deleted after photos are.

---

### Dev notes:

This plugin is more specifically a [BuddyBoss platform plugin](https://github.com/buddyboss/buddyboss-platform-addon/wiki). Part of its code is therefore mainly integration boilerplate.

As any Wordpress plugin, the entry point is the main file `buddyboss-photo-categorization.php`. The different features have been isolated into dedicated folders to try and keep a modular structure.

The administration panel and photo upload integration is a bit hacky, because although BuddyBoss does register Wordpress actions and filters on the backend, it doesn't have any customization support that I am aware of for the frontend. The graphic elements specific to PhotoCat are therefore added through javascript triggers modifying the DOM's content and adding triggers.

The features added through shortcode have a more "classic" structure, and are separed in `SmartyPHP` template views, PHP backend controller, and JavaScript/jQuery frontend controller.

The database table `bp_photos_collections_items_categories` isn't currently used and can be considered as a 'work-in-progress' item.
