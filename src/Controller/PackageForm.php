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
use Ge\Panel\Widget\Form;
use Ge\Panel\Helper\ExtForm;
use Ge\Mvc\Module\BaseModule;
use Ge\Panel\Widget\EditWindow;
use Ge\Panel\Controller\FormController;

/**
 * Контроллер информации о теме.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Marketplace\Themes\Controller
 * @since 1.0
 */
class PackageForm extends FormController
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
    protected string $defaultModel = 'PackageForm';

    /**
     * {@inheritdoc}
     */
    public function createWidget(): EditWindow
    {
        /** @var EditWindow $window */
        $window = parent::createWidget();

        // окно компонента (Ext.window.Window Sencha ExtJS)
        $window->title = '#{package.title}';
        $window->titleTpl = '#{package.titleTpl}';
        $window->width = 520;
        $window->autoHeight = true;
        $window->layout = 'fit';
        $window->resizable = false;
        $window->iconCls = 'g-icon-svg g-icon-m_info';

        // панель формы (Ge.view.form.Panel GeJS)
        $window->form->autoScroll = true;
        $window->form->router->setAll([
            'id'    => Ge::$app->request->get('id'),
            'route' => $this->module->route('/package'),
            'state' => Form::STATE_UPDATE,
            'rules' => [
                'update' => '{route}/update?id={id}',
                'data'   => '{route}/data?id={id}'
            ] 
        ]);
        $window->form->setStateButtons(
            Form::STATE_UPDATE,
            ExtForm::buttons(['help' => ['subject' => 'package'], 'save', 'cancel'])
        );
        $window->form->bodyPadding = 7;
        $window->form->defaults = [
            'xtype' => 'textfield',
            'labelAlign' => 'right',
            'labelWidth' => 120,
            'width' => '100%'
        ];
        $window->form->items = [
            [
                'name'       => 'version',
                'fieldLabel' => '#Version',
                'width'      => 200,
                'allowBlank' => false
            ],
            [
                'name'       => 'name',
                'fieldLabel' => '#Name',
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
                'maxLength'  => 255
            ],
            [
                'name'       => 'keywords',
                'fieldLabel' => '#Keywords'
            ]
        ];
        return $window;
    }
}
