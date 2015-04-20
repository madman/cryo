<?php
namespace Storage\Controller\Frontend;

use Core\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Storage\Entity\Blood;
use Storage\Form\BloodForm;

class BloodController extends Controller
{
    
    public function actionEdit($id)
    {
        return $this->update($this->app['db.bloods']->findById($id));
    }

    public function actionCreate()
    {
        return $this->update(new Blood);
    }

    protected function update(Blood $blood)
    {
        $formType = new BloodForm();
        $form = $this->app['form.factory']->createBuilder($formType, $blood)->getForm();

        if ($this->isPost()) {
            $form->handleRequest($this->app->request);

            if ($form->isValid()) {

                $this->app['db.bloods']->save($blood);

                $this->app->session->getFlashBag()->add('messages', [
                    'type'    => 'success',
                    'message' => 'Зміни збережено'
                ]);

                return $this->redirect($this->app->path('blood/create'));
            }
        }

        return $this->render('edit', [
            'form' => $form->createView()
        ]);
    }
}