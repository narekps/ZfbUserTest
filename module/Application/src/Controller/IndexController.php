<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Http\Header\SetCookie;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }

    /**
     * Change locale and redirect to home or url
     *
     * @return \Zend\Http\Response
     */
    public function changeLocaleAction()
    {
        $locales = \Application\Module::LOCALES;
        $locale = $this->params()->fromQuery('locale', '');
        $redirect = $this->params()->fromQuery('redirect', null);

        if (!isset($locales[ $locale ])) {
            $locale = \Application\Module::DEFAULT_LOCALE;
        }

        $cookie = new SetCookie('locale', $locale, time() + 365 * 60 * 60 * 24, '/');
        /** @var \Zend\Http\PhpEnvironment\Response $response */
        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeader($cookie);

        if (!empty($redirect)) {
            return $this->redirect()->toUrl($redirect);
        } else {
            return $this->redirect()->toRoute('home');
        }
    }
}
