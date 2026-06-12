<?php
/**
 * Этот файл является частью расширения модуля веб-приложения RosGear.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

namespace Rg\Backend\Marketplace\Themes\Controller;

use Ge;
use Ge\Panel\Http\Response;
use Ge\Mvc\Module\BaseModule;
use Ge\Panel\Controller\FormController;
use Rg\Backend\Marketplace\Themes\Widget\CreateWindow;

/**
 * Контроллер формы создания новой темы.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Marketplace\Themes\Controller
 * @since 1.0
 */
class CreateForm extends FormController
{
    /**
     * {@inheritdoc}
     * 
     * @var BaseModule|\Rg\Backend\Marketplace\Themes\Extension
     */
    public BaseModule $module;

    /**
     * {@inheritdoc}
     */
    protected string $defaultModel = 'CreateForm';

    /**
     * {@inheritdoc}
     * 
     * @return CreateWindow
     */
    public function createWidget(): CreateWindow
    {
        return new CreateWindow();
    }

    /**
     * Действие "view" выводит интерфейс окна создания новой темы.
     * 
     * @return Response
     */
    public function viewAction(): Response
    {
        /** @var \Ge\Panel\Http\Response $response */
        $response = $this->getResponse();

        /** @var \Rg\Backend\Marketplace\Themes\Model\CreateForm $model */
        $model = $this->getModel($this->defaultModel);
        if ($model === false) {
            $response
                ->meta->error(Ge::t('app', 'Could not defined data model "{0}"', [$this->defaultModel]));
            return $response;
        }

        if ($this->useAppEvents) {
            Ge::$app->doEvent($this->makeAppEventName(), [$this, $model]);
        }

        // валидация темы перед выводом формы
        if (!$model->validateBeforeView()) {
            $response
                ->meta->error($model->getError());
            return $response;
        }

        /** @var null|array $info Информация о выбранной теме */
        $info = $model->getThemeInfo();

        /** @var \Ge\Panel\Widget\EditWindow $widget */
        $widget = $this->getWidget();
        $widget->info = [
            'id'        => Ge::$app->request->getPost('id'),
            'name'      => 'New ' . $info['name'],
            'localPath' => '/new-' . mb_strtolower(str_replace(' ', '-', $info['name']))
        ];

        $response
            ->setContent($widget->run())
            ->meta
                ->addWidget($widget);

        if ($this->useAppEvents) {
            Ge::$app->doEvent($this->makeAppEventName('After'), [$this, $model]);
        }
        return $response;
    }

    /**
     * Действие "complete" завершает создание темы.
     * 
     * @return Response
     */
    public function completeAction(): Response
    {
        /** @var \Ge\Panel\Http\Response $response */
        $response = $this->getResponse();
        /** @var \Ge\Http\Request $request */
        $request = Ge::$app->request;

        /** @var \Rg\Backend\Marketplace\Themes\Model\CreateForm $model */
        $model = $this->getModel($this->defaultModel);
        if ($model === false) {
            $response
                ->meta->error(Ge::t('app', 'Could not defined data model "{0}"', [$this->defaultModel]));
            return $response;
        }

        if ($this->useAppEvents) {
            Ge::$app->doEvent($this->makeAppEventName(), [$this, $model]);
        }

        // загрузка атрибутов в модель из запроса
        if (!$model->load($request->getPost())) {
            $response
                ->meta->error(Ge::t(BACKEND, 'No data to perform action'));
            return $response;
        }

        // валидация атрибутов темы
        if (!$model->validate()) {
            $response
                ->meta->error(Ge::t(BACKEND, 'Error filling out form fields: {0}', [$model->getError()]));
            return $response;
        }

        // валидация выбранной темы
        if (!$model->validateTheme()) {
            $response
                ->meta->error($model->getError());
            return $response;
        }

        // создание темы
        if (!$model->create()) {
            $response
                ->meta->error(
                    $model->hasErrors() ? $model->getError() : $this->module->t('Unable to create theme')
                );
            return $response;
        }

        if ($this->useAppEvents) {
            Ge::$app->doEvent($this->makeAppEventName('After'), [$this, $model]);
        }
        return $response;
    }
}
