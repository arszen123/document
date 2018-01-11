<?php
/**
 * Created by PhpStorm.
 * User: after8
 * Date: 1/10/18
 * Time: 12:21 PM
 */

namespace Document\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DocumentController extends AbstractActionController
{
    public function indexAction()
    {
        $viewModel = new ViewModel();

        return $viewModel;
    }
}