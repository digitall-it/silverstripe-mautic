<?php

namespace Digitall\Mautic\Extensions;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\FormAction;


class SiteConfig extends DataExtension
{

    private static $db = [
        'username' => 'Varchar'
    ];
    private static $allowed_actions = [
        'RefreshSegments',
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldsToTab("Root.Mautic",
            [
                TextField::create("username", "User Name"),
            ]
        );
    }

    public function updateCMSActions(FieldList $actions)
    {
        parent::updateCMSActions($actions);

//        $actions->push(
//            new CustomAction("RefreshSegments", "Refresh Segments")
//        );
        return $actions;

    }

    public function RefreshSegments($data, Form $form)
    {
        //die('test');
        //return $this->redirectBack();
        return 'OK!';
    }
}
