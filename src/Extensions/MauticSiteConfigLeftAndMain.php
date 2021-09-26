<?php

namespace Digitall\Mautic\Extensions;

use SilverStripe\Admin\LeftAndMain;
use SilverStripe\Core\Extension;
use SilverStripe\Core\Injector\Injector;

class MauticSiteConfigLeftAndMain extends Extension
{
    public function doFlushMauticCache($data, $form)
    {
        Injector::inst()->get('Mautic')->flush();
    }
}
