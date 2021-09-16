<?php

namespace Reference\DataObjects;

use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\TextField;
use Reference\Pages\ReferencePage;
use Reference\DataObjects\Reference;

class ReferenceCategory extends DataObject
{
    private static $tablename = "ReferenceCategory";
    private static $db = [
        'Title' =>  'Text',
        'Sort'  =>  'Int'
    ];
    private static $has_one = [
        'ReferencePage' =>  ReferencePage::class
    ];
    private static $belongs_many_many = [
        'References'    =>  Reference::class
    ];
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName([
           "ReferencePageID",
           "Title",
           "Sort"
        ]);
        $fields->addFieldToTab('Root.Main',TextField::create('Title','Titel'));

        return $fields;
    }
}
