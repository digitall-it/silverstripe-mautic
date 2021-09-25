<?php

namespace Digitall\Mautic\Extensions;

use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormAction;

class MauticUserFormExtension extends DataExtension
{
    /**
     * @var array Fields on the user defined form page.
     */
    private static $db = [
        'PushToMautic' => 'Boolean',
        'MauticSegment' => 'Varchar',
        'MauticCheckboxFieldName' => 'Varchar',
        'MauticFieldMapping' => 'Text'
    ];

    public function updateFormOptions(FieldList $options)
    {
//        $options->add(TextField::create('Txt1', 'text 1'));
        //$options->add(CheckboxField::create('PushToMautic', 'Push to Mautic'));
//        $options->add(TextField::create('Txt2', 'text 2'));
        $options->add(CheckboxField::create('PushToMautic', 'Push to Mautic'));
        $options->add(TextField::create('MauticCheckboxFieldName', 'Checkbox field name')
            ->setDescription('Check if this form field if true before pushing to Mautic')
        );
        $options->add(DropdownField::create('MauticSegment', 'Mautic Segment')
            ->setDescription('Update your segment list from the Mautic configuration page')
        );
        $options->add(TextareaField::create('MauticFieldMapping', 'Mautic Field Mapping')
            ->setDescription('Check module documentation for syntax')
        );
    }


}
