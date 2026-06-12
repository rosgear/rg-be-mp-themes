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
use Rg\Backend\Marketplace\Themes\Widget\InstallWindow;

/**
 * Контроллер установки темы.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Marketplace\Themes\Controller
 * @since 1.0
 */
class InstallForm extends FormController
{
    /**
     * {@inheritdoc}
     * 
     * @var BaseModule|\Rg\Backend\Marketplace\Themes\Extension
     */
    public BaseModule $module;

    /**
     * {@inheritdoc}
     * 
     * @return InstallWindow
     */
    public function createWidget(): InstallWindow
    {
        return new InstallWindow();
    }

    /**
     * Действие "complete" завершает установку темы.
     * 
     * @return Response
     */
    public function completeAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();
        /** @var string|null $themeId Идентификатор темы */
        $themeId = Ge::$app->request->getPost('id');

        // если тема не выбрана
        if ($themeId === null) {
            $response
                ->meta->error(Ge::t('app', 'Parameter "{0}" not specified', ['id']));
            return $response;
        }

        $chunks = explode('::', $themeId);
        if (sizeof($chunks) < 2) {
            $response
                ->meta->error(Ge::t('app', 'Parameter "{0}" not specified', ['id']));
            return $response;
        }
        /** @var array $themeId Идентификатор темы */
        $themeId = [
            'side' => $chunks[0],
            'name' => $chunks[1]
        ];

        /** @var \Ge\Theme\Theme $theme */
        $theme = Ge::$app->createThemeBySide($themeId['side']);
        if ($theme === null) {
            $response
                ->meta->error(Ge::t('app', 'Parameter "{0}" not specified', ['id']));
            return $response;
        }

        /** @var array $found Найденные темы */
        $found = $theme->find(['name' => $themeId['name']]);
        // если тема не найдена
        if (sizeof($found) === 0) {
            $response
                ->meta->error($this->module->t('The theme you have chosen "{0}" does not exist', [$themeId['name']]));
            return $response;
        }

        /** @var array $info Информациия о теме */
        $info = $found[0];
    
        /** @var null|array $themeParams Параметры выбранной темы из идентификатора */
        $themeParams = Ge::$app->unifiedConfig->get($theme->unifiedName);
        if ($themeParams === null) {
            $themeParams = [
                'side'      => $themeId['side'],
                'default'   => $theme->default,
                'available' => $theme->available
            ];
        }

        // добавляем информацию об установленной теме
        $themeParams['available'][$info['name']] = [
            'name'      => $info['name'],
            'localPath' => $info['localPath']
        ];

        /** @var bool $result */
        $result = Ge::$app->unifiedConfig
            ->set($theme->unifiedName, $themeParams)
            ->save();

        if ($result) {
            // всплывающие сообщение
            $response
                ->meta->cmdPopupMsg(
                    $this->module->t('Subject "{0}" successfully added', [$info['name']]), 
                    $this->module->t('Installing the theme'), 
                    'success'
                );
            // обновить список
            $this->cmdReloadGrid();
        } else
            $response
                ->meta->error($this->module->t('Unable to install theme (error writing to file)')); 
        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function viewAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();
        /** @var string|null $themeId Идентификатор темы */
        $themeId = Ge::$app->request->getPost('id');

        // если тема не выбрана
        if ($themeId === null) {
            $response
                ->meta->error(Ge::t('app', 'Parameter "{0}" not specified', ['id']));
            return $response;
        }

        $chunks = explode('::', $themeId);
        if (sizeof($chunks) < 2) {
            $response
                ->meta->error(Ge::t('app', 'Parameter "{0}" not specified', ['id']));
            return $response;
        }
        /** @var array $themeId Идентификатор темы */
        $themeId = [
            'side' => $chunks[0],
            'name' => $chunks[1]
        ];

        /** @var \Ge\Theme\Theme $theme */
        $theme = Ge::$app->createThemeBySide($themeId['side']);
        if ($theme === null) {
            $response
                ->meta->error(Ge::t('app', 'Parameter "{0}" not specified', ['id']));
            return $response;
        }
    
        // если тема уже установлена
        if ($theme->exists($themeId['name'])) {
            $response
                ->meta->error($this->module->t('Your chosen theme "{0}" is already installed', [$themeId['name']]));
            return $response;
        }

        /** @var array $found Найденные темы */
        $found = $theme->find(['name' => $themeId['name']]);
        // если тема не найдена
        if (sizeof($found) === 0) {
            $response
                ->meta->error($this->module->t('The theme you have chosen "{0}" does not exist', [$themeId['name']]));
            return $response;
        }

        /** @var \Ge\Panel\Widget\EditWindow $widget */
        $widget = $this->getWidget();
        $widget->info = $found[0];
        // идентификатор темы
        $widget->info['id'] = Ge::$app->request->getPost('id');

        $response
            ->setContent($widget->run())
            ->meta
                ->addWidget($widget);
        return $response;
    }
}
