<?php
/**
 * test route
 */

$app->group('/test', function() use ($app, $log) {

    $app->get('/', function() use ($app, $log) {
        $app->redirect('/');
/*
        $tp = $app->view->fetch("test.php", array("aa"=>"bb"));
        $app->render('test2.php', array('tp'=>$tp));*/

    });

    $app->get('/strategy', function() use ($app, $log) {
        $stg = new \Model\Strategy($app->db);

        $search['is_open'] = '0';
        $search['is_operate'] = '1';
        $search['is_delete'] = '0';

        $strategies = $stg->getList($search);
// echo count($strategies);
       // ?? __v($strategies);
    });
});
