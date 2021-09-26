<?php

namespace Digitall\Mautic\Extensions;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\PasswordField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\FormAction;


class MauticSiteConfig extends DataExtension
{
    const MAPPING_DEFAULT = 60*60*24*30; // Cache for 30 days

    private static $defaults = [
        'MauticFieldMapping' => 'ABC'
    ];

    private static $db = [
        'MauticActive' => 'Boolean',
        'MauticURL' => 'Varchar',
        'MauticUsername' => 'Varchar',
        'MauticPassword' => 'Varchar',
        'MauticFieldMapping' => 'Text'
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldsToTab("Root.Mautic",
            [
                CheckboxField::create("MauticActive", "Mautic link active"),
                TextField::create("MauticURL", "Mautic URL"),
                TextField::create("MauticUsername", "Mautic Username"),
                PasswordField::create("MauticPasswordField", "Mautic Password")
                ->setDescription('Existing password will not show up'),
                TextareaField::create("MauticFieldMapping", "Default form mapping")
            ]
        );
    }

    public function updateCMSActions(FieldList $actions)
    {
        parent::updateCMSActions($actions);

        $actions->push(
            FormAction::create("doFlushMauticCache", "Flush Mautic Cache")->addExtraClass('btn action btn-secondary')
        );
        return $actions;

    }

    public function onBeforeWrite()
    {

        parent::onBeforeWrite();
        if (!empty($this->owner->MauticPasswordField)) $this->owner->MauticPassword=$this->owner->MauticPasswordField;
    }
}
