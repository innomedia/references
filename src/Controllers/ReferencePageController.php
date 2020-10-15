<?php
namespace Reference\Pages;

use PageController;
use SilverStripe\ORM\PaginatedList;
use Reference\DataObjects\Reference;
use SilverStripe\Dev\Debug;
use SilverStripe\View\ArrayData;

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
        $pageSize = 10;
        $posts->setPageLength($pageSize);

        // Set current page
        $start = $this->owner->request->getVar($posts->getPaginationGetVar());
        $posts->setPageStart($start);

        return $posts;
    }

    public function reference() {
        $reference = Reference::get()->filter("URLSegment",$this->request->latestParam('ID'));
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