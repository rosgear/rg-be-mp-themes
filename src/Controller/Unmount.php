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
use Ge\Panel\Controller\BaseController;

/**
 * Контроллер демонтажа (удаления) установленной темы без удаления ёё из репозитория.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Marketplace\Themes\Controller
 * @since 1.0
 */
class Unmount extends BaseController
{
    /**
     * Действие "complete" выполняет завершение установки темы.
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
    
        // если тема не найдена
        if (!$theme->exists($themeId['name'])) {
            $response
                ->meta->error($this->module->t('The theme you have chosen "{0}" does not exist', [$themeId['name']]));
            return $response;
        }

        // если тема по умолчанию
        if ($theme->default === $themeId['name']) {
            $response
                ->meta->error(
                    $this->module->t('It is impossible to dismantle (delete) the theme "{0}", because it is the current one', [$themeId['name']])
                );
            return $response;  
        }

        /** @var null|array $themeParams Параметры выбранной темы из идентификатора */
        $themeParams = Ge::$app->unifiedConfig->get($theme->unifiedName);
        if ($themeParams === null) {
            $themeParams = [
                'side'      => $themeId['side'],
                'default'   => $theme->default,
                'available' => $theme->available
            ];
        }

        // удаляем информацию об установленной теме
        unset($themeParams['available'][$themeId['name']]);

        /** @var bool $result */
        $result = Ge::$app->unifiedConfig
            ->set($theme->unifiedName, $themeParams)
            ->save();

        if ($result) {
            $response
                ->meta
                    // всплывающие сообщение
                    ->cmdPopupMsg(
                        $this->module->t('The theme "{0}" has been successfully unmounted', [$themeId['name']]), 
                        $this->module->t('Unmounting the theme'), 
                        'success'
                    )
                    // обновление списка
                    ->command('reloadGrid', $this->module->viewId('grid'));
        } else
            $response
                ->meta->error($this->module->t('Unable to unmount theme (error writing to file)')); 
        return $response;
    }
}
