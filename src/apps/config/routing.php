<?php

$container['router'] = function() use ($defaultModule, $modules) {

	$router = new \Phalcon\Mvc\Router(false);
	$router->clear();

	$router->add(
		'/dashboard/post/show/{postid:[0-9]+}',
		[
			'namespace' => 'Its\Example\Dashboard\Presentation\Web\Controller',
			'module' => 'dashboard',
			'controller' => 'post',
			'action' => 'show'
		]
	);

	$router->add(
		'/dashboard/post/delete/{postid:[0-9]+}',
		[
			'namespace' => 'Its\Example\Dashboard\Presentation\Web\Controller',
			'module' => 'dashboard',
			'controller' => 'post',
			'action' => 'delete'
		]
	);

	$router->add(
		'/dashboard/post/edit/{postid:[0-9]+}',
		[
			'namespace' => 'Its\Example\Dashboard\Presentation\Web\Controller',
			'module' => 'dashboard',
			'controller' => 'post',
			'action' => 'edit'
		]
	);

	$router->add(
		'/dashboard/post/save/{postid:[0-9]+}',
		[
			'namespace' => 'Its\Example\Dashboard\Presentation\Web\Controller',
			'module' => 'dashboard',
			'controller' => 'post',
			'action' => 'save'
		]
	);

	$router->add(
		'/dashboard/comment/upvote/{commentid:[0-9]+}',
		[
			'namespace' => 'Its\Example\Dashboard\Presentation\Web\Controller',
			'module' => 'dashboard',
			'controller' => 'comment',
			'action' => 'upvote'
		]
	);

	$router->add(
		'/dashboard/comment/create/{postid:[0-9]+}',
		[
			'namespace' => 'Its\Example\Dashboard\Presentation\Web\Controller',
			'module' => 'dashboard',
			'controller' => 'comment',
			'action' => 'create'
		]
	);

	$router->add(
		'/dashboard/comment/delete/{commentid:[0-9]+}',
		[
			'namespace' => 'Its\Example\Dashboard\Presentation\Web\Controller',
			'module' => 'dashboard',
			'controller' => 'comment',
			'action' => 'delete'
		]
	);

	/**
	 * Default Routing
	 */
	$router->add('/', [
	    'namespace' => $modules[$defaultModule]['webControllerNamespace'],
		'module' => $defaultModule,
	    'controller' => isset($modules[$defaultModule]['defaultController']) ? $modules[$defaultModule]['defaultController'] : 'index',
	    'action' => isset($modules[$defaultModule]['defaultAction']) ? $modules[$defaultModule]['defaultAction'] : 'index'
	]);
	
	/**
	 * Not Found Routing
	 */
	$router->notFound(
		[
			'namespace' => 'Its\Common\Controller',
			'controller' => 'error',
			'action'     => 'route404',
		]
	);

	/**
	 * Error Routing
	 */
	$router->addGet('/forbidden', [
		'namespace' => "Its\Common\Controller",
		'controller' => "error",
		'action' => "route403"
	]);
	
	$router->addGet('/error', [
		'namespace' => "Its\Common\Controller",
		'controller' => "error",
		'action' => "routeErrorCommon"
	]);
	
	$router->addGet('/expired', [
		'namespace' => "Its\Common\Controller",
		'controller' => "error",
		'action' => "routeErrorState"
	]);

	$router->addGet('/maintenance', [
		'namespace' => "Its\Common\Controller",
		'controller' => "error",
		'action' => "maintenance"
	]);

	/**
	 * Module Routing
	 */
	foreach ($modules as $moduleName => $module) {

		if ($module['defaultRouting'] == true) {
			/**
			 * Default Module routing
			 */
			$router->add('/'. $moduleName . '/:params', array(
				'namespace' => $module['webControllerNamespace'],
				'module' => $moduleName,
				'controller' => isset($module['defaultController']) ? $module['defaultController'] : 'index',
				'action' => isset($module['defaultAction']) ? $module['defaultAction'] : 'index',
				'params'=> 1
			));
			
			$router->add('/'. $moduleName . '/:controller/:params', array(
				'namespace' => $module['webControllerNamespace'],
				'module' => $moduleName,
				'controller' => 1,
				'action' => isset($module['defaultAction']) ? $module['defaultAction'] : 'index',
				'params' => 2
			));

			$router->add('/'. $moduleName . '/:controller/:action/:params', array(
				'namespace' => $module['webControllerNamespace'],
				'module' => $moduleName,
				'controller' => 1,
				'action' => 2,
				'params' => 3
			));	

			/**
			 * Default API Module routing
			 */
			$router->add('/'. $moduleName . '/api/{version:^(\d+\.)?(\d+\.)?(\*|\d+)$}/:params', array(
				'namespace' => $module['apiControllerNamespace'] . "\\" . 1,
				'module' => $moduleName,
				'version' => 1,
				'controller' => isset($module['defaultController']) ? $module['defaultController'] : 'index',
				'action' => isset($module['defaultAction']) ? $module['defaultAction'] : 'index',
				'params'=> 2
			));
			
			$router->add('/'. $moduleName . '/api/{version:^(\d+\.)?(\d+\.)?(\*|\d+)$}/:controller/:params', array(
				'namespace' => $module['apiControllerNamespace'] . "\\" . 1,
				'module' => $moduleName,
				'version' => 1,
				'controller' => 2,
				'action' => isset($module['defaultAction']) ? $module['defaultAction'] : 'index',
				'params' => 3
			));

			$router->add('/'. $moduleName . '/api/{version:^(\d+\.)?(\d+\.)?(\*|\d+)$}/:controller/:action/:params', array(
				'namespace' => $module['apiControllerNamespace'] . "\\" . 1,
				'module' => $moduleName,
				'version' => 1,
				'controller' => 2,
				'action' => 3,
				'params' => 4
			));	
		} else {
			
			$webModuleRouting = APP_PATH . '/modules/'. $moduleName .'/config/routes/web.php';
			
			if (file_exists($webModuleRouting) && is_file($webModuleRouting)) {
				include $webModuleRouting;
			}

			$apiModuleRouting = APP_PATH . '/modules/'. $moduleName .'/config/routes/api.php';
			
			if (file_exists($apiModuleRouting) && is_file($apiModuleRouting)) {
				include $apiModuleRouting;
			}
		}
	}
	
    $router->removeExtraSlashes(true);
    
	return $router;
};