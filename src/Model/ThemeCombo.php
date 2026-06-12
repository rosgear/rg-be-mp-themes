<?php
/**
 * Этот файл является частью модуля веб-приложения RosGear.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

 namespace Rg\Backend\Marketplace\Themes\Model;

use Ge;
use Ge\Helper\Str;
use Ge\Theme\Theme;
use Ge\Mvc\Module\BaseModule;
use Ge\Panel\Data\Model\Combo\ComboModel;

/**
 * Модель данных выпадающего списка тем.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Marketplace\Themes\Model
 * @since 1.0
 */
class ThemeCombo extends ComboModel
{
    /**
     * {@inheritdoc}
     * 
     * @var BaseModule|\Rg\Backend\Marketplace\Themes\Extension
     */
    public BaseModule $module;

    /**
     * Значок (отсутствующий) темы.
     *
     * @var string
     */
    protected string $thumbNone;

    /**
     * Параметр передаваемый HTTP-запросом, указывающий принадлежность темы к одной 
     * из сторон (сайт, панель управления).
     * 
     * Параметр передаётся с помощью метода GET и определяется {@see ThemeCombo::defineSide()}.
     * 
     * @var string
     */
    public string $sideParam = 'side';

    /**
     * Принадлежность темы к одной из сторон (сайт, панель управления).
     * 
     * Определяется через параметр передаваемый HTTP-запросом {@see ThemeCombo::defineSide()}.
     * 
     * @var string
     */
    public string $side = '';

    /**
     * Значение принадлежность темы к одной из сторон (сайт, панель управления) по умолчанию.
     * 
     * Используется в том случаи, если значение параметра {@see ThemeCombo::$sideParam} 
     * отсутствует в HTTP-запросе.
     * 
     * Может иметь значения: 'backend', 'frontend', 'all'.
     * 
     * @var string
     */
    public string $defaultSide = 'all';

    /**
     * Параметр передаваемый HTTP-запросом, указывающий, возвращать ли список тем с 
     * подставленным префиксом в их названии.
     * 
     * Параметр передаётся с помощью метода GET и определяется {@see ThemeCombo::defineSidePrefix()}.
     * 
     * @var string
     */
    public string $sidePrefixParam = 'prefix';

    /**
     * Использовать ли префикс в названии возвращаемых тем.
     * 
     * Определяется через параметр передаваемый HTTP-запросом {@see ThemeCombo::defineSidePrefix()}.
     * 
     * Необходимо для определения принадлежности темы (одной из сторон) в 
     * Панели управления с помощью JS.
     * 
     * @var bool
     */
    public bool $sidePrefix = true;

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this->thumbNone = $this->module->getAssetsUrl() . '/images/icon-none.svg';
        // принадлежность темы к одной из сторон
        $this->side = $this->defineSide();
        // использовать префикс в названии тем
        $this->sidePrefix = $this->defineSidePrefix();
    }

    /**
     * Определение принадлежности темы к одной из сторон (сайт, панель управления).
     * 
     * @return string
     */
    protected function defineSide(): string
    {
        $side = Ge::$app->request->getQuery($this->sideParam, '');
        if ($side === FRONTEND || $side === BACKEND || $side === 'all') {
            return $side;
        }
        return $this->defaultSide;
    }

    /**
     * Определение использования префикса в названии тем.
     * 
     * @return bool
     */
    protected function defineSidePrefix(): bool
    {
        $usePrefix = Ge::$app->request->getQuery($this->sidePrefixParam, null);
        if ($usePrefix === null) {
            return $this->sidePrefix;
        }
        $usePrefix = (int) $usePrefix;
        return $this->sidePrefix = $usePrefix === 1;
    }

    /**
     * Возвращает список тем.
     *
     * @param string $side Сторона: FRONTEND, BACKEND.
     * @param Theme $theme Тема.
     * @param string $status Назначение темы.
     * 
     * @return array
     */
    protected function getThemes(string $side, Theme $theme, string $status): array
    {
        $rows = [];
        foreach ($theme->available as $name => $params) {
            /** @var null|\Ge\Theme\ThemePackage $package */
            $package = $theme->getPackage($name);
            /** @var null|array $info Информация о пакете */
            $info = $package->getInfo();
            if ($info) {
                $rows[] = [
                    'id'          => $this->sidePrefix ? $side . '::' . $name : $name,
                    'name'        => $name,
                    'description' => Str::ellipsis($info['description'] ?? '', 0, 90),
                    'thumb'       => $theme->getThumbUrl($name) ?: $this->thumbNone,
                    'status'      => $status,
                    'subname'     => $name === $theme->default ? '<span>(' . $this->t('active') . ')</span>' : ''
                ];
            }
        }
        return $rows;
    }

    /**
     * {@inheritdoc}
     */
    public function getRows(): array
    {
        if ($this->side === 'all')
            $rows = array_merge(
                $this->getThemes(FRONTEND, Ge::$app->createFrontendTheme(), Ge::t(BACKEND, FRONTEND_NAME)),
                $this->getThemes(BACKEND, Ge::$app->createBackendTheme(), Ge::t(BACKEND, BACKEND_NAME))
            );
        else
        if ($this->side === FRONTEND)
            $rows = $this->getThemes(FRONTEND, Ge::$app->createFrontendTheme(), Ge::t(BACKEND, FRONTEND_NAME));
        else
        if ($this->side === BACKEND)
            $rows = $this->getThemes(BACKEND, Ge::$app->createBackendTheme(), Ge::t(BACKEND, BACKEND_NAME));
        else
            $rows = [];
        return [
            'total' => sizeof($rows),
            'rows'  => $rows
        ];
    }
}
