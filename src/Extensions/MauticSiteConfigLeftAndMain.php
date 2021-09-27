<?php

namespace Digitall\Mautic\Extensions;

use SilverStripe\Admin\LeftAndMain;
use SilverStripe\Core\Extension;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Control\Controller;

class MauticSiteConfigLeftAndMain extends Extension
{
    public function doFlushMauticCache($data, $form)
    {
        Injector::inst()->get('Mautic')->flush();
        Controller::curr()->getResponse()->setStatusCode(
            200,
            'Mautic cache flushed'
        );
    }
}
