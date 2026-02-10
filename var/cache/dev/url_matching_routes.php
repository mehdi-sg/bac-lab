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
        '/chapitre' => [[['_route' => 'chapitre_index', '_controller' => 'App\\Controller\\ChapitreController::index'], null, ['GET' => 0], null, true, false, null]],
        '/chapitre/new' => [[['_route' => 'chapitre_new', '_controller' => 'App\\Controller\\ChapitreController::new'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/chatbot' => [[['_route' => 'chatbot_page', '_controller' => 'App\\Controller\\ChatbotController::index'], null, null, null, false, false, null]],
        '/chatbot/ask' => [[['_route' => 'chatbot_ask', '_controller' => 'App\\Controller\\ChatbotController::ask'], null, ['POST' => 0], null, false, false, null]],
        '/fiche' => [[['_route' => 'fiche_index', '_controller' => 'App\\Controller\\FicheController::index'], null, ['GET' => 0], null, true, false, null]],
        '/fiche/new' => [[['_route' => 'fiche_new', '_controller' => 'App\\Controller\\FicheController::new'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/filiere' => [[['_route' => 'filiere_index', '_controller' => 'App\\Controller\\FiliereController::index'], null, ['GET' => 0], null, true, false, null]],
        '/filiere/new' => [[['_route' => 'filiere_new', '_controller' => 'App\\Controller\\FiliereController::new'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/revision/cours' => [[['_route' => 'app_revision_filieres', '_controller' => 'App\\Controller\\FrontRevisionController::listFilieres'], null, null, null, false, false, null]],
        '/home' => [[['_route' => 'app_home', '_controller' => 'App\\Controller\\HomeController::index'], null, null, null, false, false, null]],
        '/matiere' => [[['_route' => 'matiere_index', '_controller' => 'App\\Controller\\MatiereController::index'], null, ['GET' => 0], null, true, false, null]],
        '/matiere/new' => [[['_route' => 'matiere_new', '_controller' => 'App\\Controller\\MatiereController::new'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
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
                .'|/chapitre/([^/]++)(?'
                    .'|(*:223)'
                    .'|/(?'
                        .'|edit(*:239)'
                        .'|delete(*:253)'
                    .')'
                .')'
                .'|/fi(?'
                    .'|che/(?'
                        .'|([^/]++)(?'
                            .'|(*:287)'
                            .'|/(?'
                                .'|edit(*:303)'
                                .'|history(*:318)'
                            .')'
                        .')'
                        .'|fiche/([^/]++)/delete(*:349)'
                    .')'
                    .'|liere/([^/]++)(?'
                        .'|(*:375)'
                        .'|/(?'
                            .'|edit(*:391)'
                            .'|delete(*:405)'
                        .')'
                    .')'
                .')'
                .'|/revision/cours/(?'
                    .'|filiere/([^/]++)(*:451)'
                    .'|matiere/([^/]++)(*:475)'
                .')'
                .'|/matiere/([^/]++)(?'
                    .'|(*:504)'
                    .'|/(?'
                        .'|edit(*:520)'
                        .'|delete(*:534)'
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
        223 => [[['_route' => 'chapitre_show', '_controller' => 'App\\Controller\\ChapitreController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        239 => [[['_route' => 'chapitre_edit', '_controller' => 'App\\Controller\\ChapitreController::edit'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        253 => [[['_route' => 'chapitre_delete', '_controller' => 'App\\Controller\\ChapitreController::delete'], ['id'], ['POST' => 0], null, false, false, null]],
        287 => [[['_route' => 'fiche_show', '_controller' => 'App\\Controller\\FicheController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        303 => [[['_route' => 'fiche_edit', '_controller' => 'App\\Controller\\FicheController::edit'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        318 => [[['_route' => 'fiche_history', '_controller' => 'App\\Controller\\FicheController::history'], ['id'], ['GET' => 0], null, false, false, null]],
        349 => [[['_route' => 'fiche_delete', '_controller' => 'App\\Controller\\FicheController::delete'], ['id'], ['POST' => 0], null, false, false, null]],
        375 => [[['_route' => 'filiere_show', '_controller' => 'App\\Controller\\FiliereController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        391 => [[['_route' => 'filiere_edit', '_controller' => 'App\\Controller\\FiliereController::edit'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        405 => [[['_route' => 'filiere_delete', '_controller' => 'App\\Controller\\FiliereController::delete'], ['id'], ['POST' => 0], null, false, false, null]],
        451 => [[['_route' => 'app_revision_matieres', '_controller' => 'App\\Controller\\FrontRevisionController::listMatieres'], ['id'], null, null, false, true, null]],
        475 => [[['_route' => 'app_revision_pdf', '_controller' => 'App\\Controller\\FrontRevisionController::listPdf'], ['id'], null, null, false, true, null]],
        504 => [[['_route' => 'matiere_show', '_controller' => 'App\\Controller\\MatiereController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        520 => [[['_route' => 'matiere_edit', '_controller' => 'App\\Controller\\MatiereController::edit'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        534 => [
            [['_route' => 'matiere_delete', '_controller' => 'App\\Controller\\MatiereController::delete'], ['id'], ['POST' => 0], null, false, false, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
