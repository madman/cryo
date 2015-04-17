<?php
namespace Users\Controller\Frontend;

use Core\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Users\Component\PasswordChanger;
use Users\Form\ProfileForm;

class ManageController extends Controller
{

    public function actionEdit($id)
    {
        return $this->update($this->loadUserOr404($id));
    }

    public function actionCreate()
    {
        return $this->update(new Game);
    }

    public function actionDelete($id)
    {
        $user = $this->loadUserOr404($id);
        $this->app['db.users']->remove($user);

        return $this->app->json(['result' => 'success']);
    }

    public function actionList()
    {
        return $this->render('list', ['users' => $this->app['db.users']->findAll()]);
    }

    protected function update(Game $game)
    {
        $formType = new GameForm(
            $this->app['config']->get('image/games'),
            $this->app['config']->get('games/runners'),
            $this->app->translator
        );
        $form = $this->app['form.factory']->createBuilder($formType, $game)->getForm();

        if ($this->isPost()) {
            $form->handleRequest($this->app->request);

            if ($form->isValid()) {
                if ($game->getRunner() !== 'as3') {
                    $game->setRunnerBaseUrl('');
                }

                $game->save();

                $imagesUploader = new AdditionalImagesManager($this->app, $game);
                $imagesUploader->upload();

                $this->app->session->getFlashBag()->add('messages', [
                    'type'    => 'success',
                    'message' => $this->app->trans('Изменения успешно сохранены')
                ]);

                return $this->redirect(
                    $this->app->path('admin/games/' . ($game->getIsMobile() ? 'mobile' : 'index'))
                );
            }
        }

        return $this->render('edit', [
            'form' => $form->createView()
        ]);
    }
}
