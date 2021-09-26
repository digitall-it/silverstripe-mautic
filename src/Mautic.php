<?php

use Mautic\Auth\ApiAuth;
use Mautic\MauticApi;
use SilverStripe\Core\Injector\Injector;
use Silverstripe\SiteConfig\SiteConfig;
use Psr\SimpleCache\CacheInterface;
use SilverStripe\Core\Flushable;

class Mautic implements Flushable
{
    const CACHE_TTL = 60*60*24*30; // Cache for 30 days

    public function __construct()
    {
        $this->_contextapi = array();

        $this->_cache = Injector::inst()->get(CacheInterface::class . '.mautic');
    }

    public static function flush()
    {
        Injector::inst()->get(CacheInterface::class . '.mautic')->clear();
    }

    public function getSegmentsAsKeyValue()
    {
        $array = array();
        $segments=$this->getSegments();
        $lists = $segments["lists"];
        foreach ($lists as $list) $array[$list['alias']] = $list['name'];
        return $array;
    }

    public function getSettings()
    {
        if (!isset($this->_settings)) {
            $config = SiteConfig::current_site_config();

            $this->_settings = array(
                'userName' => $config->MauticUsername,
                'password' => $config->MauticPassword,
            );
            $this->_apiURL = $config->MauticURL . "/api/";
        }
        return $this->_settings;
    }


    public function getAPI()
    {
        return Injector::inst()->get('Mautic\MauticApi');
    }

    public function getAuth()
    {

        if (!isset($this->_apiauth)) {
            $this->_apiauth = Injector::inst()->get('Mautic\Auth\ApiAuth')
                ->newAuth($this->getSettings(), 'BasicAuth');
        }
        return $this->_apiauth;
    }


    public function getContextAPI($context)
    {
        if (!array_key_exists($context, $this->_contextapi)) {
            $this->_contextapi[$context] = Injector::inst()->get('Mautic\MauticApi')
                ->newApi($context, $this->getAuth(), $this->_apiURL);
        }
        return $this->_contextapi[$context];
    }

    public function getSegments()
    {
        if(!isset($this->_segments)) {
            if ($this->_cache->has('segments')) {
                $this->_segments = unserialize($this->_cache->get('segments', self::CACHE_TTL));
            } else {
                $this->_segments = $this->getContextAPI('segments')->getList();
                $this->_cache->set('segments', serialize($this->_segments));
            }
        }

        return $this->_segments;
    }

    public function getMappings()
    {
        if (!isset($this->_mappings)) {
            $this->_mappings = SiteConfig::current_site_config()->MauticFieldMapping;
        }

        $mappings = preg_split('/\r\n|\r|\n/', $this->_mappings);

        foreach ($mappings as $mapping) {
            $mapping_array = explode(':', $mapping);
            $mappings_array[$mapping_array[0]] = $mapping_array[1];
        }
        return isset($mappings_array) ? $mappings_array : array();
    }

    public function addContact(array $data)
    {
        $contactAPI = $this->getContextAPI('contacts');

        $mappings = $this->getMappings();

        foreach ($mappings as $form_key => $mautic_key) {
            if (array_key_exists($form_key, $data)) $mauticData[$mautic_key] = $data[$form_key];
        }
        if (isset($mauticData)) {
            $response = $contactAPI->create($mauticData);
            $contact = $response[$contactAPI->itemName()];

            return $contact;
        } else {
            user_error('Mapping mismatch.', E_USER_ERROR);
        }
    }

    public function addContactToSegment($contact, $segment_alias)
    {
        $segmentAPI = $this->getContextAPI('segments');

        $segmentID = $this->getSegmentID($segment_alias);

        $response = $segmentAPI->addContact($segmentID, $contact["id"]);

        if (!isset($response['success'])) {
            user_error('Cannot add contact to segment.', E_USER_ERROR);
        }
    }

    public function addOrUpdateContactSegments($contact, $segment_alias)
    {

        if (!$this->isContactInSegment($contact, $segment_alias)) $this->addContactToSegment($contact, $segment_alias);
    }

    public function isContactInSegment($contact, $segment_alias)
    {
        $contact_segments = $this->getContactSegments($contact);
        $return = false;
        if ($contact_segments['total'] > 0) {
            foreach ($contact_segments['lists'] as $contact_segment) {
                if ($contact_segment['alias'] == $segment_alias) {
                    $return = true;
                    break;
                }
            }
        }
        return $return;
    }

    public function getSegmentID($alias)
    {
        $segments = $this->getSegments();
        $lists = $segments["lists"];
        foreach ($lists as $list) {
            if ($list['alias'] == $alias) return $list["id"];
        }
    }

    public function getContactByEmailAddress($email)
    {
        $contactAPI = $this->getContextAPI('contacts');
        $contacts = $contactAPI->getList('email:' . $email);
        if (array_key_exists('total', $contacts) && $contacts["total"] >= 1 && array_key_exists('contacts', $contacts)) $contact = reset($contacts['contacts']);
        return isset($contact) ? $contact : false;
    }

    public function getContactSegments($contact)
    {
        $contactAPI = $this->getContextAPI('contacts');
        $response = $contactAPI->getContactSegments($contact["id"]);
        return $response;
    }
}
