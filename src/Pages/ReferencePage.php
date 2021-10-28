<?php

namespace Reference\Pages;

use Page;
use Reference\DataObjects\Reference;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\GridField\GridField;
use Reference\DataObjects\ReferenceCategory;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;

class ReferencePage extends Page
{
    private static $tablename = "ReferencePage";
    private static $description = 'Hiermit können Sie eine Referenzseite erstellen - Referenzen werden direkt im Module gepflegt';
    private static $db = array(
        'ShowPerPage' => 'Int(10)',
        'SortField' => "Varchar(255)",
        'SortOrder' => "Enum('ASC, DESC', 'DESC')",
        'Content' => 'HTMLText'
    );
    private static $defaults = array(
        'ShowPerPage' => 10,
        'SortField' => 'Created',
        'SortOrder' => 'DESC',
    );
    private static $has_many = [
        'References' => Reference::class,
        'ReferenceCategories' => ReferenceCategory::class,
    ];
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldToTab('Root.Main', HTMLEditorField::create('Content', 'Inhalt'));

        $fields->addFieldToTab(
            'Root.Referenzen',
            GridField::create(
                'References',
                'Referenzen',
                $this->References()->sort("Sort ASC"),
                GridFieldConfig_RecordEditor::create(20)->addComponent(new GridFieldOrderableRows("Sort"))
            )
        );

        if (Config::inst()->get("ReferenceModuleConfig")["CategoriesEnabled"]) {
            $ReferenceCategories = null;
            if(array_key_exists("ContainCategoriesInPage",Config::inst()->get("ReferenceModuleConfig")) && Config::inst()->get("ReferenceModuleConfig")["ContainCategoriesInPage"])
            {
                $ReferenceCategories = $this->ReferenceCategories()->sort("Sort ASC");
            }
            else
            {
                $ReferenceCategories = ReferenceCategory::get()->sort("Sort ASC");
            }
            $fields->addFieldToTab(
                'Root.Kategorien',
                GridField::create(
                    'ReferenceCategories',
                    'Kategorien',
                    $ReferenceCategories,
                    GridFieldConfig_RecordEditor::create(20)->addComponent(new GridFieldOrderableRows("Sort"))
                )
            );
        }
        
        $this->extend('updateReferencePageCMSFields', $fields);

        return $fields;
    }
}
