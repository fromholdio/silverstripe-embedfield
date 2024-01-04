SilverStripe Embed Field
===================================

This field is designed to let users attached an oembed object (eg a YouTube video) to a page or dataobject.  It stores the oembed result information in an EmbedObject for easy access from the template (or wherever you want it).

This is largely built upon the work of [SilverStripe Embed Field nathancox/embedfield](https://github.com/nathancox/silverstripe-embedfield) which has been quiet for several years.

**This version 4.x changes the composer package vendor & name, and the namespace of the EmbedObject and EmbedField classes.**

This has styling updates, Silverstripe v5 and Embed/Embed v4 compatibility and enhancements, and a few other minor tweaks.

Requirements
------------
* SilverStripe 5.0+

Installation Instructions
-------------------------

1. Install with composer `composer require fromholdio/silverstripe-embedfield`
2. Visit yoursite.com/dev/build to rebuild the database

Usage Overview
--------------

Make a has_one relationship to an EmbedObject then create an EmbedField in getCMSFields:

```php
namespace {

    use SilverStripe\CMS\Model\SiteTree;
    use Fromholdio\EmbedField\Model\EmbedObject;
    use Fromholdio\EmbedField\Forms\EmbedField;

    class Page extends SiteTree
    {
        private static $db = [];

        private static $has_one = [
            'MyVideo' => EmbedObject::class
        ];
        
        public function getCMSFields() {
            $fields = parent::getCMSFields();
            
            $fields->addFieldToTab('Root.Main', EmbedField::create('MyVideoID', 'Sidebar video'));
            
            return $fields;
        }
    }
}
```

In the page template the video can now be embedded with `$MyVideo`.
