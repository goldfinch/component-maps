<?php

namespace Goldfinch\Component\Maps\Models;

use BetterBrief\GoogleMapField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\TextField;
use SilverStripe\Security\Permission;
use SilverStripe\ORM\FieldType\DBHTMLText;
use Goldfinch\Component\Maps\Models\MapSegment;
use Goldfinch\JSONEditor\Forms\JSONEditorField;
use Goldfinch\JSONEditor\ORM\FieldType\DBJSONText;

class MapMarker extends DataObject
{
    private static $table_name = 'MapMarker';
    private static $singular_name = 'marker';
    private static $plural_name = 'markers';

    private static $db = [
        'Title' => 'Varchar',
        'Latitude' => 'Varchar',
        'Longitude' => 'Varchar',
        'Zoom' => 'Int',

        'Parameters' => DBJSONText::class,
    ];

    private static $has_one = [
        'Segment' => MapSegment::class,
    ];

    private static $summary_fields = [
        'MapThumbnail' => 'Map',
        'Title' => 'Title',
        'Created' => 'Received at',
        'Segment.Type' => 'Type',
    ];

    // private static $belongs_to = [];
    // private static $has_many = [];
    // private static $many_many = [];
    // private static $many_many_extraFields = [];
    // private static $belongs_many_many = [];
    // private static $default_sort = null;
    // private static $indexes = null;
    // private static $owns = [];
    // private static $casting = [];
    // private static $defaults = [];

    // private static $summary_fields = [];
    // private static $field_labels = [];
    // private static $searchable_fields = [];

    // private static $cascade_deletes = [];
    // private static $cascade_duplicates = [];

    // * goldfinch/helpers
    // private static $field_descriptions = [];
    // private static $required_fields = [];

    public function MapThumbnail()
    {
        $url = google_maps_preview($this->Latitude, $this->Longitude, $this->Zoom, '260x140');

        $html = DBHTMLText::create();
        $html->setValue('<img src="' . $url . '" alt="Preview image"/>');

        return $html;
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields = $fields->makeReadonly();

        $fields->removeByName([
            'SegmentID',
            'Parameters',
        ]);

        $fields->addFieldsToTab(
            'Root.Main',
            [
                TextField::create('Title', 'Title'),
                GoogleMapField::create($this, 'Location'),
            ],
        );

        if ($this->ID)
        {
            $schemaParamsPath = BASE_PATH . '/app/_schema/mapmarker.json';

            if (file_exists($schemaParamsPath))
            {
                $schemaParams = file_get_contents($schemaParamsPath);

                $fields->addFieldsToTab(
                    'Root.Settings',
                    [
                        JSONEditorField::create('Parameters', 'Parameters', $this, [], '{}', null, $schemaParams),
                    ]
                );
            }
        }

        return $fields;
    }

    // public function validate()
    // {
    //     $result = parent::validate();

    //     // $result->addError('Error message');

    //     return $result;
    // }

    // public function onBeforeWrite()
    // {
    //     // ..

    //     parent::onBeforeWrite();
    // }

    // public function onBeforeDelete()
    // {
    //     // ..

    //     parent::onBeforeDelete();
    // }

    // public function canView($member = null)
    // {
    //     return Permission::check('CMS_ACCESS_Company\Website\MyAdmin', 'any', $member);
    // }

    // public function canEdit($member = null)
    // {
    //     return Permission::check('CMS_ACCESS_Company\Website\MyAdmin', 'any', $member);
    // }

    // public function canDelete($member = null)
    // {
    //     return Permission::check('CMS_ACCESS_Company\Website\MyAdmin', 'any', $member);
    // }

    // public function canCreate($member = null, $context = [])
    // {
    //     return Permission::check('CMS_ACCESS_Company\Website\MyAdmin', 'any', $member);
    // }
}
