<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Accounts\Controller\Login'     => 'Accounts\Controller\LoginController',
            'Accounts\Controller\Register'  => 'Accounts\Controller\RegisterController',
            'Accounts\Controller\Dashboard' => 'Accounts\Controller\DashboardController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'login' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/accounts',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Accounts\Controller',
                        'controller'    => 'Login',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action[/:param]]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
            'logout' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/accounts/logout',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Accounts\Controller',
                        'controller'    => 'Login',
                        'action'        => 'logout',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'accounts' => __DIR__ . '/../view',
        ),
    ),
);
