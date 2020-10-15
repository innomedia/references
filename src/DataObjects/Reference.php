<?php

namespace Reference\DataObjects;

use SiteTreeLinkHelper;
use SilverStripe\Assets\Image;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\TextField;
use Reference\Pages\ReferencePage;
use SilverStripe\TagField\TagField;
use SilverStripe\Core\Config\Config;
use Reference\DataObjects\ReferenceCategory;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;

class Reference extends DataObject
{
    private static $tablename = "Reference";
    private static $db = [
        'Title' => 'Text',
        'SubTitle' => 'Text',
        'Unternehmen' => 'Text',
        'Content' => 'HTMLText',
        'Date' => 'Date',
        'Sort' => 'Int',
        'URLSegment' => 'Varchar(255)',
        'Webseite' => 'Text',
    ];

    private static $has_one = [
        'ReferencePage' => ReferencePage::class,
        'Image' => Image::class
    ];
    private static $many_many = [
        'ReferenceCategories' => ReferenceCategory::class
    ];

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        $changedFields = $this->getChangedFields(true);
        if(array_key_exists("Title",$changedFields))
        {
            if($changedFields["Title"]["after"] != "" && $changedFields["Title"]["after"] != $changedFields["Title"]["before"])
            {
                $this->URLSegment = $this->constructURLSegment();
            }
        }
    }

    private function constructURLSegment()
    {
        return $this->cleanLink(strtolower(str_replace(" ", "-", $this->Title)));
    }

    private function cleanLink($string)
    {
        $string = str_replace("ä", "ae", $string);
        $string = str_replace("ü", "ue", $string);
        $string = str_replace("ö", "oe", $string);
        $string = str_replace("Ä", "Ae", $string);
        $string = str_replace("Ü", "Ue", $string);
        $string = str_replace("Ö", "Oe", $string);
        $string = str_replace("ß", "ss", $string);
        $string = str_replace("&", "and", $string);
        $string = str_replace(["´", ",", ":", ";"], "", $string);
        return $string;
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName([
            "Sort",
            'ReferencePageID'
        ]);
        $fields->addFieldsToTab('Root.Main', [
            TextField::create('Title', 'Titel'),
            TextField::create(  'SubTitle', 'Subtitel'),
            TextField::create(  'Unternehmen', 'Unternehmen oder Presse'),
            HTMLEditorField::create('Content', 'Inhalt'),
            TextField::create('Webseite', 'Webseite'),
        ]);


        if (Config::inst()->get("ReferenceModuleConfig")["CategoriesEnabled"]) {
            $fields->addFieldToTab(
                'Root.Main',
                TagField::create(
                    'ReferenceCategories',
                    'Kategorien',
                    ReferenceCategory::get()->filter('ReferencePageID', $this->ReferencePageID),
                    $this->ReferenceCategories()
                )
                    ->setShouldLazyLoad(true)// tags should be lazy loaded
                    ->setCanCreate(false)
            );
        }

        return $fields;
    }

    public function Link($action_ = null)
    {
        return $this->ReferencePage()->Link() . "reference/" . $this->URLSegment;
    }
}
