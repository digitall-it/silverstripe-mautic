<?php

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\ORM\DataExtension;


class SiteConfig extends DataExtension
{

    private static $db = [
        'username' => 'Text'
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldToTab("Root.Main",
            new HTMLEditorField("username", "User Name")
        );
    }
}
