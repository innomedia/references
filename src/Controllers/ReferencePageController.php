<?php
namespace Reference\Pages;

use PageController;
use SilverStripe\ORM\PaginatedList;
use Reference\DataObjects\Reference;
use SilverStripe\Dev\Debug;
use SilverStripe\View\ArrayData;
use SilverStripe\Core\Config\Config;

class ReferencePageController extends PageController
{
    private static $allowed_actions = [
        "PaginatedList",
        "reference"
    ];
    public function PaginatedList()
    {
        $allPosts = $this->owner->References()->sort('Sort ASC') ?: new ArrayList();

        $posts = new PaginatedList($allPosts);

        // Set appropriate page size
        $pageSize = (Config::inst()->get('ReferenceModuleConfig')["Pagesize"])?Config::inst()->get('ReferenceModuleConfig')["Pagesize"]:9;
        $posts->setPageLength($pageSize);

        // Set current page
        $start = $this->owner->request->getVar($posts->getPaginationGetVar());
        $posts->setPageStart($start);

        return $posts;
    }

    public function reference() {
        $reference = Reference::get()->filter([
            "URLSegment" => $this->request->latestParam('ID'),
            "ReferencePageID"   =>  $this->dataRecord->ID
        ]);
        if(count($reference) == 1) {
            $templateData = [
                "Reference" => $reference->First(),
                'BackLink'		=> (($this->request->getHeader('Referer')) ? $this->request->getHeader('Referer') : $this->Link()),
            ];
            return $this->customise(new ArrayData($templateData))->renderWith(["Reference","Page"]);
        } else {
            $this->httpError(404);
            return false;
        }
	}
}
