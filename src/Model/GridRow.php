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
use Closure;
use Ge\Db\Sql\Where;
use Ge\Panel\Data\Model\FormModel;
use Ge\Panel\Data\Model\Exception\ColumnException;

/**
 * Модель данных профиля записи темы.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Marketplace\Themes\Model
 * @since 1.0
 */
class GridRow extends FormModel
{
    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this
            ->on(self::EVENT_AFTER_SAVE, function ($isInsert, $columns, $result, $message) {
                if ($message['success']) {
                    $message['title']   = $this->module->t('Setting the default theme');
                    $message['message'] = $this->module->t(
                        'The theme "{0}" is set as the default for "{1}"', 
                        [$this->default,  $this->side ? Ge::t('app', ucfirst($this->side)) : SYMBOL_NONAME]
                    );
                }
                /** @var \Ge\Panel\Controller\GridController $controller */
                $controller = $this->controller();
                // обновить список
                $controller->cmdReloadGrid();
                // всплывающие сообщение
                $this->response()
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type']);
            });
    }

    /**
     * {@inheritdoc}
     */
    public function maskedAttributes(): array
    {
        return [
            'side'      => 'side', // сторона: `BACKEND`, `FRONTEND`
            'available' => 'available', // доступные темы
            'default'   => 'default' // имя активной темы
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeValidate(array &$attributes): bool
    {
        // если попытка столбцу "По умолчанию" вернуть флаг в false (это не логично, 
        // но и заблокировать не возможно)
        if (isset($attributes['default']) && $attributes['default'] == 0) {
            throw new ColumnException($this->t('In the "By default" column, you can only select the default theme with the switch, but not disable'));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function updateRecord(array $columns, Where|Closure|string|array $where = null): false|int
    {
        /** @var null|array $identifier */
        $identifier = $this->getIdentifier();
        if ($identifier === null) {
            return false;
        }

        /** @var null|\Ge\Theme\Theme */
        $theme = Ge::$app->createThemeBySide($identifier['side']);
        // если тема не определилась
        if ($theme === null) {
            return false;
        }

        // текущая тема
        $this->attributes['default'] = $identifier['name'];
        return Ge::$app->unifiedConfig
            ->set($theme->unifiedName, $this->attributes)
            ->save();
    }

    /**
     * {@inheritdoc}
     * 
     * @return null|array
     */
    public function getIdentifier(): ?array
    {
        /** @var string $theme Тема: {side}::{name} */
        $theme = Ge::$app->request->get('theme', '');
        if ($theme) {
            $chunks = explode('::', $theme);
            if (sizeof($chunks) > 1) {
                return [
                    'side' => $chunks[0],
                    'name' => $chunks[1]
                ];
            }
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function get(mixed $identifier = null): ?static
    {
        if ($identifier === null) {
            $identifier = $this->getIdentifier();
        }

        if ($identifier) {
            /** @var null|\Ge\Theme\Theme */
            $theme = Ge::$app->createThemeBySide($identifier['side']);
            // если тема не определилась
            if ($theme === null) {
                return null;
            }

            /** @var null|array $row Параметры выбранной темы из идентификатора */
            $row = Ge::$app->unifiedConfig->get($theme->unifiedName);
            if ($row === null) {
                $row = [
                    'side'      => $identifier['side'],
                    'default'   => $theme->default,
                    'available' => $theme->available
                ];
            }

            $this->reset();
            $this->afterSelect();
            $this->populate($this, $row);
            $this->afterPopulate();
            return $this;
        }
        return null;
    }
}
