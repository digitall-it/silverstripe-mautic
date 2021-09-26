<?php

namespace Digitall\Mautic\Extensions;

use SilverStripe\UserForms\Model\Submission\SubmittedForm;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Core\Injector\Injector;
use Silverstripe\SiteConfig\SiteConfig;

class MauticUserFormSubmittedExtension extends DataExtension
{
    public function updateAfterProcess()
    {
        if (!SiteConfig::current_site_config()->MauticActive) return;
        if ($this->owner->Parent->PushToMautic) {

            $fields = array();
            foreach ($this->owner->Values() as $field) $fields[$field->Name] = $field->Value;

            if ($this->owner->Parent->MauticCheckboxFieldName != "" &&
                array_key_exists($this->owner->Parent->MauticCheckboxFieldName, $fields) &&
                $fields[$this->owner->Parent->MauticCheckboxFieldName] != 'Yes'
            ) return;

            $Mautic = Injector::inst()->get('Mautic');
            $Mautic->setAuth(SiteConfig::current_site_config());
            $fieldsMapped = $Mautic->MapFields($fields, SiteConfig::current_site_config()->MauticFieldMapping);
            if (count($fieldsMapped) > 0) $Mautic->addOrUpdateContact($fieldsMapped, $this->owner->Parent->MauticSegment);
        }
    }

}
