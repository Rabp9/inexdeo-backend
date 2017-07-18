<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Core\Plugin;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

/**
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 *
 */
Router::defaultRouteClass(DashedRoute::class);

Router::scope('/', function (RouteBuilder $routes) {
    /**
     * Here, we are connecting '/' (base path) to a controller called 'Pages',
     * its action called 'display', and we pass a param to select the view file
     * to use (in this case, src/Template/Pages/home.ctp)...
     */
    $routes->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);

    /**
     * ...and connect the rest of 'Pages' controller's URLs.
     */
    $routes->connect('/pages/*', ['controller' => 'Pages', 'action' => 'display']);

    /**
     * Connect catchall routes for all controllers.
     *
     * Using the argument `DashedRoute`, the `fallbacks` method is a shortcut for
     *    `$routes->connect('/:controller', ['action' => 'index'], ['routeClass' => 'DashedRoute']);`
     *    `$routes->connect('/:controller/:action/*', [], ['routeClass' => 'DashedRoute']);`
     *
     * Any route class can be used with this method, such as:
     * - DashedRoute
     * - InflectedRoute
     * - Route
     * - Or your own route class
     *
     * You can remove these routes once you've connected the
     * routes you want in your application.
     */
    $routes->fallbacks('DashedRoute');
});

/**
 * Load all plugin routes. See the Plugin documentation on
 * how to customize the loading of plugin routes.
 */
Router::scope('/', function ($routes) {
    $routes->extensions(['json']);
    $routes->resources('Productos', [
        'map' => [
            'getRandom/:num' => [
                'action' => 'getRandom',
                'method' => 'GET'
            ],
            'getAdmin' => [
                'action' => 'getAdmin',
                'method' => 'GET'
            ],
            'preview/' => [
                'action' => 'preview',
                'method' => 'POST'
            ], 
            'deleteImage' => [
                'action' => 'deleteImage',
                'method' => 'POST'
            ],  
            'previewBrochure/' => [
                'action' => 'previewBrochure',
                'method' => 'POST'
            ], 
            'previewPortada/' => [
                'action' => 'previewPortada',
                'method' => 'POST'
            ], 
            'remove' => [
                'action' => 'remove',
                'method' => 'POST'
            ]
        ]
    ]);
    $routes->resources('Servicios', [
        'map' => [
            'getRandom/:num' => [
                'action' => 'getRandom',
                'method' => 'GET'
            ],
            'getAdmin' => [
                'action' => 'getAdmin',
                'method' => 'GET'
            ],
            'preview/' => [
                'action' => 'preview',
                'method' => 'POST'
            ], 
            'deleteImage' => [
                'action' => 'deleteImage',
                'method' => 'POST'
            ],  
            'previewBrochure/' => [
                'action' => 'previewBrochure',
                'method' => 'POST'
            ], 
            'previewPortada/' => [
                'action' => 'previewPortada',
                'method' => 'POST'
            ], 
            'remove' => [
                'action' => 'remove',
                'method' => 'POST'
            ]
        ]
    ]);
    $routes->resources('Proyectos', [
        'map' => [
            'getRandom/:num' => [
                'action' => 'getRandom',
                'method' => 'GET'
            ],
            'getAdmin' => [
                'action' => 'getAdmin',
                'method' => 'GET'
            ],
            'preview/' => [
                'action' => 'preview',
                'method' => 'POST'
            ], 
            'deleteImage' => [
                'action' => 'deleteImage',
                'method' => 'POST'
            ],  
            'previewBrochure/' => [
                'action' => 'previewBrochure',
                'method' => 'POST'
            ], 
            'previewPortada/' => [
                'action' => 'previewPortada',
                'method' => 'POST'
            ], 
            'remove' => [
                'action' => 'remove',
                'method' => 'POST'
            ]
        ]
    ]);
    $routes->resources('Albumes');
    $routes->resources('Politicas', [
        'map' => [
            'preview/' => [
                'action' => 'preview',
                'method' => 'POST'
            ],
            'getAdmin' => [
                'action' => 'getAdmin',
                'method' => 'GET'
            ]
        ]
    ]);
    $routes->resources('Infos', [
        'map' => [
            'saveMany' => [
                'action' => 'saveMany',
                'method' => 'POST'
            ],
            'getData/:data' => [
                'action' => 'getData',
                'method' => 'GET'
            ],
            'getDataMany' => [
                'action' => 'getDataMany',
                'method' => 'POST'
            ],
            'getDataByData' => [
                'action' => 'getDataByData',
                'method' => 'POST'
            ]
        ]
    ]);
    $routes->resources('Proyectos', [
        'map' => [
            'getRandom/:num' => [
                'action' => 'getRandom',
                'method' => 'GET'
            ]
        ]
    ]);
    $routes->resources('Servicios', [
        'map' => [
            'getRandom/:num' => [
                'action' => 'getRandom',
                'method' => 'GET'
            ]
        ]
    ]);
    $routes->resources('Clientes', [
        'map' => [
            'getAdmin' => [
                'action' => 'getAdmin',
                'method' => 'GET'
            ],
            'getCiudades' => [
                'action' => 'getCiudades',
                'method' => 'GET'
            ],
            'getRubros' => [
                'action' => 'getRubros',
                'method' => 'GET'
            ],
            'getClientesByCiudad/:ciudad' => [
                'action' => 'getClientesByCiudad',
                'method' => 'GET'
            ],
            'getClientesByRubro/:rubro' => [
                'action' => 'getClientesByRubro',
                'method' => 'GET'
            ]
        ]
    ]);
    $routes->resources('Convocatorias', [
        'map' => [
            'preview/' => [
                'action' => 'preview',
                'method' => 'POST'
            ],
            'getAdmin' => [
                'action' => 'getAdmin',
                'method' => 'GET'
            ]
        ]
    ]);
    $routes->resources('TipoSugerencias', [
        'map' => [
            'sendMessage/' => [
                'action' => 'sendMessage',
                'method' => 'POST'
            ],
            'getAdmin' => [
                'action' => 'getAdmin',
                'method' => 'GET'
            ],
            'removeDetalle' => [
                'action' => 'removeDetalle',
                'method' => 'POST'
            ]
        ]
    ]);
    $routes->resources('Pages', [
        'map' => [
            'getAdmin' => [
                'action' => 'getAdmin',
                'method' => 'GET'
            ],
            'getPages/:type' => [
                'action' => 'getPages',
                'method' => 'GET'
            ],
            'upload/' => [
                'action' => 'upload',
                'method' => 'POST'
            ]
        ]
    ]);
    $routes->resources('Asesorias', [
        'map' => [
            'getAdmin' => [
                'action' => 'getAdmin',
                'method' => 'GET'
            ],
            'upload/' => [
                'action' => 'upload',
                'method' => 'POST'
            ]
        ]
    ]);
    $routes->resources('Roles', [
        'map' => [
            'getAdmin' => [
                'action' => 'getAdmin',
                'method' => 'GET'
            ]
        ]
    ]);
    $routes->resources('Users', [
        'map' => [
            'getAdmin' => [
                'action' => 'getAdmin',
                'method' => 'GET'
            ],
            'login' => [
                'action' => 'login',
                'method' => 'POST'
            ],
            'token' => [
                'action' => 'token',
                'method' => 'POST'
            ]
        ]
    ]);
    $routes->resources('Controllers');
});

Plugin::routes();
