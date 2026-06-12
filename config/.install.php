<?php
/**
 * Этот файл является частью расширения модуля веб-приложения RosGear.
 * 
 * Файл конфигурации установки модуля.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c] 2015 Этот файл является частью расширения модуля веб-приложения RosGear. Web-студия
 * @license https://rosgear.ru/license/
 */

return [
    'id'          => 'rg.be.mp.themes',
    'moduleId'    => 'rg.be.mp',
    'name'        => 'Themes',
    'description' => 'Themes management',
    'namespace'   => 'Rg\Backend\Marketplace\Themes',
    'path'        => '/rg/rg.be.mp.themes',
    'route'       => 'themes',
    'locales'     => ['ru_RU', 'fr_FR', 'en_GB', 'be_BY'],
    'permissions' => ['any', 'read', 'info'],
    'events'      => [],
    'required'    => [
        ['php', 'version' => '8.2'],
        ['app', 'code' => 'RG Workspace'],
        ['app', 'code' => 'RG CMS'],
        ['app', 'code' => 'RG CRM'],
        ['module', 'id' => 'rg.be.mp']
    ]
];
