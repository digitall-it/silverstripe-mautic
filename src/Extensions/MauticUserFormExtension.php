<?php

namespace Digitall\Mautic\Extensions;

use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormAction;
use Silverstripe\SiteConfig\SiteConfig;
use SilverStripe\Core\Injector\Injector;

class MauticUserFormExtension extends DataExtension
{
    /**
     * @var array Fields on the user defined form page.
     */
    private static $db = [
        'PushToMautic' => 'Boolean',
        'MauticSegment' => 'Varchar',
        'MauticCheckboxFieldName' => 'Varchar'
    ];

    public function updateFormOptions(FieldList $options)
    {
        $options->add(CheckboxField::create('PushToMautic', 'Push to Mautic'));
        $options->add(TextField::create('MauticCheckboxFieldName', 'Checkbox field name')->setDescription('Set a checkbox merge name to check before pushing to Mautic'));

        $options->add(DropdownField::create('MauticSegment', 'Mautic Segment',
            Injector::inst()->get('Mautic')->getSegmentsAsKeyValue())
        );
    }


}
