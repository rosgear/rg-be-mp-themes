<?php
/**
 * Этот файл является частью расширения модуля веб-приложения RosGear.
 * 
 * @link https://rosgear.ru//framework/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

namespace Rg\Backend\Marketplace\Themes\Widget;

use Ge\Panel\Widget\Form;
use Ge\Panel\Widget\Window;
use Ge\Panel\Helper\ExtForm;

/**
 * Виджет формирования интерфейса окна создания темы.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Marketplace\Themes\Widget
 * @since 1.0
 */
class PackageWindow extends Window
{
    /**
     * Виджет для формирования интерфейса формы.
     * 
     * @var Form
     */
    public Form $form;

    /**
     * Информация о теме.
     * 
     * Обязательно должен содержать ключ "id", где значение может принимать:
     * 'backend::ThemeName', 'frontend::ThemeName'.
     * 
     * @var array
     */
    public array $info = [];

    /**
     * {@inheritdoc}
     */
    public array $requires = [
        'Ge.view.window.Window',
        'Ge.view.form.Panel'
    ];

    /**
     * {@inheritdoc}
     */
    protected function init(): void
    {
        parent::init();

        // панель формы (Ge.view.form.Panel GeJS)
        $this->form = new Form([
            'id'     => 'pform', // => rg-mp-themes-pform
            'router' => [
                'route' => $this->creator->route('/create'),
                'state' => Form::STATE_INSERT,
                'rules' => [
                    'add' => '{route}/complete',
                ] 
            ]
        ], $this);
        $this->form->setStateButtons(Form::STATE_INSERT,
            ExtForm::buttons(['help', 'add' => ['text' => '#Create'], 'cancel'])
        );

        // окно (Ext.window.Window Sencha ExtJS)
        $this->id         = 'rg-mp-themes-cwindow';
        $this->iconCls    = 'rg-mp-themes__icon-create_small';
        $this->cls        = 'g-window_install';
        $this->padding    = 0;
        $this->width      = 450;
        $this->autoHeight = true;
        $this->layout     = 'fit';
        $this->resizable  = false;
        $this->items      = [$this->form];
        $this->responsiveConfig = [
            'height < 450' => ['height' => '99%'],
            'width < 450' => ['width' => '99%'],
        ];
    }

    /**
     * Возвращает заголовок окна.
     * 
     * @return string
     */
    protected function renderTitle(): string
    {
        if ($this->title) return $this->title;

        return $this->creator->t('{create.title}', [$this->info['name'] ?? SYMBOL_NONAME]);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeRender(): bool
    {
        $this->title = $this->renderTitle();

        $this->form->items = [
            [
                'xtype'   => 'container',
                'layout'  => 'anchor',
                'padding' => 10,
                'defaults' => [
                    'xtype'      => 'textfield',
                    'labelAlign' => 'right',
                    'labelWidth' => 100,
                    'width'      => '100%'
                ],
                'items' => [
                    [
                        'xtype' => 'hidden',
                        'name'  => 'id',
                        'value' => $this->info['id'] ?? ''
                    ],
                    [
                        'name'       => 'localPath',
                        'fieldLabel' => '#Catalog',
                        'maxLength'  => 255,
                        'value'      => $this->info['localPath'] ?? '/new-theme',
                        'allowBlank' => false
                    ],
                    [
                        'name'       => 'name',
                        'fieldLabel' => '#Name',
                        'value'      => $this->info['name'] ?? 'New Theme',
                        'allowBlank' => false
                    ],
                    [
                        'name'       => 'description',
                        'fieldLabel' => '#Description',
                        'maxLength'  => 255
                    ],
                    [
                        'name'       => 'author',
                        'fieldLabel' => '#Author',
                        'maxLength'  => 255
                    ],
                    [
                        'name'       => 'license',
                        'fieldLabel' => '#License',
                        'maxLength'  => 255,
                        'value'      => 'GPL-2.0-or-later'
                    ],
                    [
                        'name'       => 'version',
                        'fieldLabel' => '#Version',
                        'width'      => 200,
                        'value'      => '1.0',
                        'allowBlank' => false
                    ]
                ]
            ],
            [
                'xtype' => 'label',
                'ui'    => 'note',
                'text'  => '#All files for the new theme will be copied to the directory specified in the "Directory" field. After adding all the files, the theme will be installed.'
            ]
        ];
        return true;
    }
}
