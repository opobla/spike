<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'CALM\Controller\CALM' => 'CALM\Controller\CALMController',
        ),
    ),

	'router' => array(
        'routes' => array(
            'CALM' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/CALM[/:DataBase][/:Table][/:Action][/:Param1][/:Param2][/:Param3][/:Param4]',
                    'constraints' => array(
                        'DataBase'   =>  '[a-zA-Z][a-zA-Z0-9_-]*',
			'Table'   =>  '[a-zA-Z][a-zA-Z0-9_-]*',
			'Action'   =>  '[a-zA-Z][a-zA-Z0-9_-]*',
			'Param1'   =>  '[a-zA-Z0-9][a-zA-Z0-9%+:_-]*',
			'Param2'   =>  '[a-zA-Z0-9][a-zA-Z0-9%+:_-]*',
			'Param3'   =>  '[a-zA-Z0-9][a-zA-Z0-9%+:_-]*',
			'Param4'   =>  '[a-zA-Z0-9][a-zA-Z0-9%+:_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'CALM\Controller\CALM',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),



    'view_manager' => array(
        'template_path_stack' => array(
            'CALM' => __DIR__ . '/../view',
        ),
	'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
);
