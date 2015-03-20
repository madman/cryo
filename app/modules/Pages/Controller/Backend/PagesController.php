<?php

namespace Pages\Controller\Backend;

use Core\Controller;
use Pages\Entity\StaticPage;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Core\Db\EntityNotFoundException;
use Pages\Entity\Page;
use Core\Application;

abstract class PagesController extends Controller
{
    protected $entityName;

    public function __construct(Application $app)
    {
        parent::__construct($app);
        if (null === $this->entityName) {
            $this->entityName = $this->getEntityName();
        }
    }

    public function actionIndex()
    {
        $searchQuery = $this->app->request->get('search');
        $sortBy      = $this->app->request->get('sort', 'created_at');
        $sortOrder   = $this->app->request->get('order', 'desc');
        $currentPage = $this->app->request->get('p', 1);
        $entity      = $this->getEntityClass();

        list($pages, $pagesNumber) = $entity ::manager()->search($searchQuery, $sortBy, $sortOrder, $currentPage);

        return $this->render('index', array(
            'pages'       => $pages,
            'searchQuery' => $searchQuery,
            'sortBy'      => $sortBy,
            'sortOrder'   => $sortOrder,
            'currentPage' => $currentPage,
            'pagesNumber' => $pagesNumber,
            'token'       => $this->app['form.csrf_provider']->generateCsrfToken('ajax-protection')
        ));
    }

    public function actionEdit($id)
    {
        $entity = $this->getEntityClass();

        $page = $entity::manager()->findById($id);

        if (!$page) {
            throw new EntityNotFoundException($this->app->trans('Страница не найдена'));
        }

        return $this->update($page);
    }

    public function actionCreate()
    {
        $entity = $this->getEntityClass();

        return $this->update(new $entity);
    }

    protected function update(Page $page)
    {
        $formTypeName = $this->getFormType();
        $formType     = new $formTypeName($this->app);

        $form = $this->app['form.factory']->createBuilder($formType, $page)->getForm();

        if ($this->isPost()) {

            $form->handleRequest($this->app->request);

            if ($form->isValid()) {
                $page->save();

                $this->app->session->getFlashBag()->add('messages', [
                    'type'    => 'success',
                    'message' => $this->app->trans('Изменения успешно сохранены')
                ]);

                return new RedirectResponse($this->app->url_generator->generate(
                    str_replace(['create', 'edit'], ['index', 'index'], $this->app->request->get('_route'))
                ));
            }
        }

        return $this->render('edit', [
            'form' => $form->createView()
        ]);
    }

    public function actionManage()
    {
        $entity = $this->getEntityClass();
        if ($entity instanceof StaticPage) {
            throw new \InvalidArgumentException($this->app->trans('Method is not allowed for static pages'));
        }

        $id           = $this->app->request->get('id');
        $manageAction = $this->app->request->get('action');
        $token        = $this->app->request->get('token');

        $this->checkRequiredParams($id, $manageAction, $token);

        $page = $entity::manager()->findById($id);

        if (!$page) {
            throw new EntityNotFoundException($this->app->trans('Акция не найдена'));
        }

        switch ($manageAction) {
            case 'delete':
                return $this->actionDelete($page);
                break;
            case 'toggle':
                return $this->actionToggle($page);
                break;
            default:
                throw new \InvalidArgumentException("Incorrect manage action was passed to the Controller {$manageAction}");
        }
    }

    protected function actionDelete(Page $page)
    {
        $page->remove();

        return $this->app->json(['result' => 'success']);
    }

    protected function actionToggle(Page $page)
    {
        $page->setIsActive(!$page->getIsActive());
        $page->save();

        return $this->app->json(['result' => 'success']);
    }

    public function checkRequiredParams($id, $manageAction, $token)
    {
        if (!$this->isAjax()) {
            throw new \InvalidArgumentException('Only ajax method allowed to manage actions');
        }

        if (!$id) {
            throw new \InvalidArgumentException('Required parameter is missing: id');
        }

        if (!$manageAction) {
            throw new \InvalidArgumentException('Required parameter is missing: action');
        }

        if (!$this->app['form.csrf_provider']->isCsrfTokenValid('ajax-protection', $token)) {
            throw new \InvalidArgumentException('Token is invalid');
        }
    }

    protected function getEntityName()
    {
        $reflection = new \ReflectionClass(get_called_class());

        return substr($reflection->getShortName(), 0, -strlen('sController'));
    }

    protected function getEntityClass()
    {
        return 'Pages\\Entity\\' . $this->entityName;
    }

    protected function getFormType()
    {
        return 'Pages\\Form\\' . $this->entityName . 'Form';
    }
}
