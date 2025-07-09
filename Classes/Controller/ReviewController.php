<?php
declare(strict_types=1);

namespace AyhanKoyun\IupacNomenclature\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use AyhanKoyun\IupacNomenclature\Service\ChemicalStructureService;

/**
 * ReviewController
 *
 * Provides actions for checking and displaying chemical structures.
 *
 * @author Ayhan Koyun
 * @copyright (c) 2025 Ayhan Koyun
 * @package AyhanKoyun\IupacNomenclature\Controller
 */
class ReviewController extends ActionController
{
    protected ChemicalStructureService $structureService;

    public function __construct(ChemicalStructureService $structureService)
    {
        $this->structureService = $structureService;
    }

    public function listAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }

    public function processAction(): ResponseInterface
    {
        $submittedData = $this->request->getParsedBody();
        $result = $this->structureService->getChemicalStructureFromPostData($submittedData);

        $this->view->assign('structure', $result);
        return $this->htmlResponse();
    }
}