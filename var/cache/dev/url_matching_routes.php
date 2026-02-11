<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/_wdt/styles' => [[['_route' => '_wdt_stylesheet', '_controller' => 'web_profiler.controller.profiler::toolbarStylesheetAction'], null, null, null, false, false, null]],
        '/_profiler' => [[['_route' => '_profiler_home', '_controller' => 'web_profiler.controller.profiler::homeAction'], null, null, null, true, false, null]],
        '/_profiler/search' => [[['_route' => '_profiler_search', '_controller' => 'web_profiler.controller.profiler::searchAction'], null, null, null, false, false, null]],
        '/_profiler/search_bar' => [[['_route' => '_profiler_search_bar', '_controller' => 'web_profiler.controller.profiler::searchBarAction'], null, null, null, false, false, null]],
        '/_profiler/phpinfo' => [[['_route' => '_profiler_phpinfo', '_controller' => 'web_profiler.controller.profiler::phpinfoAction'], null, null, null, false, false, null]],
        '/_profiler/xdebug' => [[['_route' => '_profiler_xdebug', '_controller' => 'web_profiler.controller.profiler::xdebugAction'], null, null, null, false, false, null]],
        '/_profiler/open' => [[['_route' => '_profiler_open_file', '_controller' => 'web_profiler.controller.profiler::openAction'], null, null, null, false, false, null]],
        '/home' => [[['_route' => 'app_home', '_controller' => 'App\\Controller\\HomeController::index'], null, null, null, false, false, null]],
        '/admin/quiz' => [[['_route' => 'admin_quiz_index', '_controller' => 'App\\Controller\\QuizAdminController::index'], null, null, null, true, false, null]],
        '/admin/quiz/nouveau' => [[['_route' => 'admin_quiz_new', '_controller' => 'App\\Controller\\QuizAdminController::new'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/quiz' => [[['_route' => 'app_quiz', '_controller' => 'App\\Controller\\QuizController::index'], null, null, null, false, false, null]],
        '/quiz/start' => [[['_route' => 'app_quiz_start', '_controller' => 'App\\Controller\\QuizController::start'], null, null, null, false, false, null]],
        '/quiz/submit' => [[['_route' => 'app_quiz_submit', '_controller' => 'App\\Controller\\QuizController::submit'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_(?'
                    .'|error/(\\d+)(?:\\.([^/]++))?(*:38)'
                    .'|wdt/([^/]++)(*:57)'
                    .'|profiler/(?'
                        .'|font/([^/\\.]++)\\.woff2(*:98)'
                        .'|([^/]++)(?'
                            .'|/(?'
                                .'|search/results(*:134)'
                                .'|router(*:148)'
                                .'|exception(?'
                                    .'|(*:168)'
                                    .'|\\.css(*:181)'
                                .')'
                            .')'
                            .'|(*:191)'
                        .')'
                    .')'
                .')'
                .'|/a(?'
                    .'|pi/chapitres/matiere/([^/]++)(*:236)'
                    .'|dmin/qu(?'
                        .'|estion/([^/]++)/choix/(?'
                            .'|add(*:282)'
                            .'|([^/]++)/(?'
                                .'|delete(*:308)'
                                .'|toggle\\-correct(*:331)'
                            .')'
                        .')'
                        .'|iz/(?'
                            .'|([^/]++)/question/nouvelle(*:373)'
                            .'|question/([^/]++)/(?'
                                .'|modifier(*:410)'
                                .'|supprimer(*:427)'
                            .')'
                            .'|([^/]++)/(?'
                                .'|modifier(*:456)'
                                .'|supprimer(*:473)'
                                .'|questions(*:490)'
                            .')'
                        .')'
                    .')'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        38 => [[['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        57 => [[['_route' => '_wdt', '_controller' => 'web_profiler.controller.profiler::toolbarAction'], ['token'], null, null, false, true, null]],
        98 => [[['_route' => '_profiler_font', '_controller' => 'web_profiler.controller.profiler::fontAction'], ['fontName'], null, null, false, false, null]],
        134 => [[['_route' => '_profiler_search_results', '_controller' => 'web_profiler.controller.profiler::searchResultsAction'], ['token'], null, null, false, false, null]],
        148 => [[['_route' => '_profiler_router', '_controller' => 'web_profiler.controller.router::panelAction'], ['token'], null, null, false, false, null]],
        168 => [[['_route' => '_profiler_exception', '_controller' => 'web_profiler.controller.exception_panel::body'], ['token'], null, null, false, false, null]],
        181 => [[['_route' => '_profiler_exception_css', '_controller' => 'web_profiler.controller.exception_panel::stylesheet'], ['token'], null, null, false, false, null]],
        191 => [[['_route' => '_profiler', '_controller' => 'web_profiler.controller.profiler::panelAction'], ['token'], null, null, false, true, null]],
        236 => [[['_route' => 'api_chapitres_by_matiere', '_controller' => 'App\\Controller\\ChapitreApiController::getByMatiere'], ['id'], ['GET' => 0], null, false, true, null]],
        282 => [[['_route' => 'admin_choix_add', '_controller' => 'App\\Controller\\ChoixController::add'], ['questionId'], ['POST' => 0], null, false, false, null]],
        308 => [[['_route' => 'admin_choix_delete', '_controller' => 'App\\Controller\\ChoixController::delete'], ['questionId', 'choixId'], ['POST' => 0], null, false, false, null]],
        331 => [[['_route' => 'admin_choix_toggle_correct', '_controller' => 'App\\Controller\\ChoixController::toggleCorrect'], ['questionId', 'choixId'], ['POST' => 0], null, false, false, null]],
        373 => [[['_route' => 'admin_question_new', '_controller' => 'App\\Controller\\QuestionAdminController::newQuestion'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        410 => [[['_route' => 'admin_question_edit', '_controller' => 'App\\Controller\\QuestionAdminController::editQuestion'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        427 => [[['_route' => 'admin_question_delete', '_controller' => 'App\\Controller\\QuestionAdminController::deleteQuestion'], ['id'], ['POST' => 0], null, false, false, null]],
        456 => [[['_route' => 'admin_quiz_edit', '_controller' => 'App\\Controller\\QuizAdminController::edit'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        473 => [[['_route' => 'admin_quiz_delete', '_controller' => 'App\\Controller\\QuizAdminController::delete'], ['id'], ['POST' => 0], null, false, false, null]],
        490 => [
            [['_route' => 'admin_quiz_questions', '_controller' => 'App\\Controller\\QuizAdminController::questions'], ['id'], null, null, false, false, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
