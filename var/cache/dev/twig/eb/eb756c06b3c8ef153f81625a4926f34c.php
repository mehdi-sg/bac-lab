<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* base.html.twig */
class __TwigTemplate_253fa1d5b1298f191665bc4bd133a977 extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'css' => [$this, 'block_css'],
            'body' => [$this, 'block_body'],
            'js' => [$this, 'block_js'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "base.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "base.html.twig"));

        // line 1
        yield "<!doctype html>
<html class=\"no-js\" lang=\"fr\">
<head>
    <meta charset=\"utf-8\">
    <meta http-equiv=\"x-ua-compatible\" content=\"ie=edge\">
    <title>";
        // line 6
        yield from $this->unwrap()->yieldBlock('title', $context, $blocks);
        yield "</title>
    <meta name=\"description\" content=\"Plateforme éducative pour les bacheliers tunisiens : révisions, ressources et calcul de score.\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
    
    <link rel=\"shortcut icon\" type=\"image/x-icon\" href=\"";
        // line 10
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/img/favicon.ico"), "html", null, true);
        yield "\">

    ";
        // line 12
        yield from $this->unwrap()->yieldBlock('css', $context, $blocks);
        // line 116
        yield "</head>

<body>
    <div id=\"preloader-active\">
        <div class=\"preloader d-flex align-items-center justify-content-center\">
            <div class=\"preloader-inner position-relative\">
                <div class=\"preloader-circle\"></div>
                <div class=\"preloader-img pere-text\">
                    <img src=\"";
        // line 124
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/img/logo/loder.png"), "html", null, true);
        yield "\" alt=\"Chargement...\">
                </div>
            </div>
        </div>
    </div>
    
    <header>
        <div class=\"header-area header-transparent\">
            <div class=\"main-header \">
                <div class=\"header-bottom header-sticky\">
                    <div class=\"container-fluid\">
                        <div class=\"row align-items-center\">
                            <div class=\"col-xl-2 col-lg-2\">
                                <div class=\"logo\">
                                    <a href=\"";
        // line 138
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_home");
        yield "\"><img  src=\"";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/img/logo/logo.png"), "html", null, true);
        yield "\" style=\"width:100px;height:100px;\" alt=\"BacLab Logo\"></a>
                                </div>
                            </div>
                            <div class=\"col-xl-10 col-lg-10\">
                                <div class=\"menu-wrapper d-flex align-items-center justify-content-end\">
                                    <div class=\"main-menu d-none d-lg-block\">
                                        <nav>
                                            <ul id=\"navigation\">                                                                                          
                                                <li><a href=\"";
        // line 146
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_home");
        yield "\">Accueil</a></li>
                                                <li><a href=\"#\">Révision</a>
                                                    <ul class=\"submenu\">
                                                        <";
        // line 150
        yield "<li><a href=\"";
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_revision_filieres");
        yield "\">Cours & PDF</a></li>
                                                        <li><a href=\"#\">Quiz & Tests</a></li>
                                                        <li><a href=\"";
        // line 152
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("fiche_index");
        yield "\">fiches & co-édition</a></li>
                                                    </ul>
                                                </li>
                                                <li><a href=\"#\">Orientation</a>
                                                    <ul class=\"submenu\">
                                                        <li><a href=\"#\">Calcul du Score</a></li>
                                                        <li><a href=\"#\">Guide des Filières</a></li>
                                                    </ul>
                                                </li>
                                                <li><a href=\"#\">Communauté</a></li>
                                                
                                                ";
        // line 163
        if ((($tmp = $this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("ROLE_USER")) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 164
            yield "                                                    <li class=\"button-header\"><a href=\"#\" class=\"btn btn3\">Mon Profil</a></li>
                                                ";
        } else {
            // line 166
            yield "                                                    <li class=\"button-header margin-left\"><a href=\"#\" class=\"btn\">S'inscrire</a></li>
                                                    <li class=\"button-header\"><a href=\"#\" class=\"btn btn3\">Connexion</a></li>
                                                ";
        }
        // line 169
        yield "                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                            </div> 
                            <div class=\"col-12\">
                                <div class=\"mobile_menu d-block d-lg-none\"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
        ";
        // line 185
        yield from $this->unwrap()->yieldBlock('body', $context, $blocks);
        // line 186
        yield "    </main>

    ";
        // line 188
        yield from $this->load("components/chatbot.html.twig", 188)->unwrap()->yield($context);
        // line 189
        yield "
    <footer>
        <div class=\"footer-wrappper footer-bg\">
            <div class=\"footer-area footer-padding\">
                <div class=\"container\">
                    <div class=\"row justify-content-between\">
                        <div class=\"col-xl-4 col-lg-5 col-md-4 col-sm-6\">
                            <div class=\"single-footer-caption mb-50\">
                                <div class=\"footer-logo mb-25\">
                                    <a href=\"#\"><img src=\"";
        // line 198
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/img/logo/logo2_footer.png"), "html", null, true);
        yield "\" alt=\"BacLab Footer\"></a>
                                </div>
                                <div class=\"footer-tittle\">
                                    <div class=\"footer-pera\">
                                        <p>Votre succès au Bac commence par une révision structurée et une orientation éclairée.</p>
                                    </div>
                                </div>
                                <div class=\"footer-social\">
                                    <a href=\"#\"><i class=\"fab fa-facebook-f\"></i></a>
                                    <a href=\"#\"><i class=\"fab fa-instagram\"></i></a>
                                    <a href=\"#\"><i class=\"fab fa-linkedin\"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class=\"col-xl-2 col-lg-3 col-md-4 col-sm-5\">
                            <div class=\"single-footer-caption mb-50\">
                                <div class=\"footer-tittle\">
                                    <h4>Révision</h4>
                                    <ul>
                                        <li><a href=\"#\">Mathématiques</a></li>
                                        <li><a href=\"#\">Sciences</a></li>
                                        <li><a href=\"#\">Informatique</a></li>
                                        <li><a href=\"#\">Économie</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class=\"col-xl-2 col-lg-4 col-md-4 col-sm-6\">
                            <div class=\"single-footer-caption mb-50\">
                                <div class=\"footer-tittle\">
                                    <h4>Orientation</h4>
                                    <ul>
                                        <li><a href=\"#\">Calculer mon Score</a></li>
                                        <li><a href=\"#\">Filières Universitaires</a></li>
                                        <li><a href=\"#\">Guide de l'Orientation</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class=\"footer-bottom-area\">
                <div class=\"container\">
                    <div class=\"footer-border\">
                        <div class=\"row d-flex align-items-center\">
                            <div class=\"col-xl-12 \">
                                <div class=\"footer-copy-right text-center\">
                                    <p>Copyright &copy; ";
        // line 246
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate("now", "Y"), "html", null, true);
        yield " BacLab - Tous droits réservés</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer> 

    ";
        // line 256
        yield from $this->unwrap()->yieldBlock('js', $context, $blocks);
        // line 347
        yield "</body>
</html>
";
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    // line 6
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_title(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "title"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "title"));

        yield "BacLab | Plateforme de Révision & Orientation";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 12
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_css(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "css"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "css"));

        // line 13
        yield "        <link rel=\"stylesheet\" href=\"";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/css/bootstrap.min.css"), "html", null, true);
        yield "\">
        <link rel=\"stylesheet\" href=\"";
        // line 14
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/css/owl.carousel.min.css"), "html", null, true);
        yield "\">
        <link rel=\"stylesheet\" href=\"";
        // line 15
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/css/slicknav.css"), "html", null, true);
        yield "\">
        <link rel=\"stylesheet\" href=\"";
        // line 16
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/css/flaticon.css"), "html", null, true);
        yield "\">
        <link rel=\"stylesheet\" href=\"";
        // line 17
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/css/progressbar_barfiller.css"), "html", null, true);
        yield "\">
        <link rel=\"stylesheet\" href=\"";
        // line 18
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/css/gijgo.css"), "html", null, true);
        yield "\">
        <link rel=\"stylesheet\" href=\"";
        // line 19
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/css/animate.min.css"), "html", null, true);
        yield "\">
        <link rel=\"stylesheet\" href=\"";
        // line 20
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/css/animated-headline.css"), "html", null, true);
        yield "\">
        <link rel=\"stylesheet\" href=\"";
        // line 21
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/css/magnific-popup.css"), "html", null, true);
        yield "\">
        <link rel=\"stylesheet\" href=\"";
        // line 22
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/css/fontawesome-all.min.css"), "html", null, true);
        yield "\">
        <link rel=\"stylesheet\" href=\"";
        // line 23
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/css/themify-icons.css"), "html", null, true);
        yield "\">
        <link rel=\"stylesheet\" href=\"";
        // line 24
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/css/slick.css"), "html", null, true);
        yield "\">
        <link rel=\"stylesheet\" href=\"";
        // line 25
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/css/nice-select.css"), "html", null, true);
        yield "\">
        <link rel=\"stylesheet\" href=\"";
        // line 26
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/css/style.css"), "html", null, true);
        yield "\">
        <style>
            #chatbotBtn {
                position: fixed;
                right: 24px;
                bottom: 24px;
                width: 56px;
                height: 56px;
                border-radius: 50%;
                border: none;
                background: #3b1a77;
                color: #fff;
                font-size: 22px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.2);
                z-index: 99999;
                pointer-events: auto;
            }
            #chatbotWindow {
                position: fixed;
                right: 24px;
                bottom: 90px;
                width: 320px;
                max-height: 420px;
                background: #fff;
                border-radius: 16px;
                box-shadow: 0 12px 30px rgba(0,0,0,0.2);
                display: none;
                flex-direction: column;
                overflow: hidden;
                z-index: 99999;
                pointer-events: auto;
            }
            #chatbotHeader {
                background: #3b1a77;
                color: #fff;
                padding: 10px 14px;
                font-weight: 600;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            #chatbotMessages {
                padding: 12px;
                overflow-y: auto;
                flex: 1;
                font-size: 14px;
            }
            .chatbot-msg {
                margin-bottom: 10px;
                line-height: 1.4;
            }
            .chatbot-msg.user {
                text-align: right;
                color: #3b1a77;
                font-weight: 600;
            }
            .chatbot-msg.bot {
                text-align: left;
                color: #333;
            }
            #chatbotInputArea {
                display: flex;
                gap: 6px;
                padding: 10px;
                border-top: 1px solid #eee;
                background: #fafafa;
            }
            #chatbotInput {
                flex: 1;
                border: 1px solid #ddd;
                border-radius: 10px;
                padding: 8px 10px;
                font-size: 14px;
            }
            #chatbotSend {
                background: #3b1a77;
                color: #fff;
                border: none;
                border-radius: 10px;
                padding: 8px 12px;
                font-size: 14px;
                pointer-events: auto;
            }
            #chatbotInput:disabled,
            #chatbotSend:disabled {
                opacity: 0.6;
                cursor: not-allowed;
            }
        </style>
    ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 185
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_body(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body"));

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 256
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_js(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "js"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "js"));

        // line 257
        yield "        <script src=\"";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/vendor/modernizr-3.5.0.min.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 258
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/vendor/jquery-1.12.4.min.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 259
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/popper.min.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 260
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/bootstrap.min.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 261
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/jquery.slicknav.min.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 262
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/owl.carousel.min.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 263
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/slick.min.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 264
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/wow.min.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 265
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/animated.headline.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 266
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/jquery.magnific-popup.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 267
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/gijgo.min.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 268
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/jquery.nice-select.min.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 269
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/jquery.sticky.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 270
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/jquery.barfiller.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 271
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/jquery.counterup.min.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 272
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/waypoints.min.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 273
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/jquery.countdown.min.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 274
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/hover-direction-snake.min.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 275
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/contact.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 276
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/jquery.form.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 277
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/jquery.validate.min.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 278
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/mail-script.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 279
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/jquery.ajaxchimp.min.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 280
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/plugins.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 281
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/main.js"), "html", null, true);
        yield "\"></script>
        <script>
            (function () {
                const btn = document.getElementById('chatbotBtn');
                const win = document.getElementById('chatbotWindow');
                const closeBtn = document.getElementById('chatbotClose');
                const input = document.getElementById('chatbotInput');
                const send = document.getElementById('chatbotSend');
                const msgs = document.getElementById('chatbotMessages');

                const addMsg = (text, who) => {
                    const div = document.createElement('div');
                    div.className = 'chatbot-msg ' + who;
                    div.textContent = text;
                    msgs.appendChild(div);
                    msgs.scrollTop = msgs.scrollHeight;
                };

                const sendMessage = async () => {
                    const text = (input.value || '').trim();
                    if (!text) return;
                    addMsg(text, 'user');
                    input.value = '';

                    try {
                        send.disabled = true;
                        input.disabled = true;
                        const res = await fetch('/chatbot/message', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ message: text })
                        });
                        let data = null;
                        try {
                            data = await res.json();
                        } catch (e) {
                            data = { message: 'Réponse invalide du serveur.' };
                        }
                        if (!res.ok) {
                            addMsg(data.message || 'Erreur serveur.', 'bot');
                        } else {
                            addMsg(data.message || 'Pas de réponse', 'bot');
                        }
                    } catch (e) {
                        addMsg('Erreur de connexion. Réessaie.', 'bot');
                    } finally {
                        send.disabled = false;
                        input.disabled = false;
                        input.focus();
                    }
                };

                btn.addEventListener('click', () => {
                    win.style.display = 'flex';
                    input.focus();
                });
                closeBtn.addEventListener('click', () => {
                    win.style.display = 'none';
                });
                send.addEventListener('click', sendMessage);
                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') sendMessage();
                });
            })();
        </script>
    ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "base.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  588 => 281,  584 => 280,  580 => 279,  576 => 278,  572 => 277,  568 => 276,  564 => 275,  560 => 274,  556 => 273,  552 => 272,  548 => 271,  544 => 270,  540 => 269,  536 => 268,  532 => 267,  528 => 266,  524 => 265,  520 => 264,  516 => 263,  512 => 262,  508 => 261,  504 => 260,  500 => 259,  496 => 258,  491 => 257,  478 => 256,  456 => 185,  355 => 26,  351 => 25,  347 => 24,  343 => 23,  339 => 22,  335 => 21,  331 => 20,  327 => 19,  323 => 18,  319 => 17,  315 => 16,  311 => 15,  307 => 14,  302 => 13,  289 => 12,  266 => 6,  253 => 347,  251 => 256,  238 => 246,  187 => 198,  176 => 189,  174 => 188,  170 => 186,  168 => 185,  150 => 169,  145 => 166,  141 => 164,  139 => 163,  125 => 152,  119 => 150,  113 => 146,  100 => 138,  83 => 124,  73 => 116,  71 => 12,  66 => 10,  59 => 6,  52 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!doctype html>
<html class=\"no-js\" lang=\"fr\">
<head>
    <meta charset=\"utf-8\">
    <meta http-equiv=\"x-ua-compatible\" content=\"ie=edge\">
    <title>{% block title %}BacLab | Plateforme de Révision & Orientation{% endblock %}</title>
    <meta name=\"description\" content=\"Plateforme éducative pour les bacheliers tunisiens : révisions, ressources et calcul de score.\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
    
    <link rel=\"shortcut icon\" type=\"image/x-icon\" href=\"{{ asset('front/img/favicon.ico') }}\">

    {% block css %}
        <link rel=\"stylesheet\" href=\"{{ asset('front/css/bootstrap.min.css') }}\">
        <link rel=\"stylesheet\" href=\"{{ asset('front/css/owl.carousel.min.css') }}\">
        <link rel=\"stylesheet\" href=\"{{ asset('front/css/slicknav.css') }}\">
        <link rel=\"stylesheet\" href=\"{{ asset('front/css/flaticon.css') }}\">
        <link rel=\"stylesheet\" href=\"{{ asset('front/css/progressbar_barfiller.css') }}\">
        <link rel=\"stylesheet\" href=\"{{ asset('front/css/gijgo.css') }}\">
        <link rel=\"stylesheet\" href=\"{{ asset('front/css/animate.min.css') }}\">
        <link rel=\"stylesheet\" href=\"{{ asset('front/css/animated-headline.css') }}\">
        <link rel=\"stylesheet\" href=\"{{ asset('front/css/magnific-popup.css') }}\">
        <link rel=\"stylesheet\" href=\"{{ asset('front/css/fontawesome-all.min.css') }}\">
        <link rel=\"stylesheet\" href=\"{{ asset('front/css/themify-icons.css') }}\">
        <link rel=\"stylesheet\" href=\"{{ asset('front/css/slick.css') }}\">
        <link rel=\"stylesheet\" href=\"{{ asset('front/css/nice-select.css') }}\">
        <link rel=\"stylesheet\" href=\"{{ asset('front/css/style.css') }}\">
        <style>
            #chatbotBtn {
                position: fixed;
                right: 24px;
                bottom: 24px;
                width: 56px;
                height: 56px;
                border-radius: 50%;
                border: none;
                background: #3b1a77;
                color: #fff;
                font-size: 22px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.2);
                z-index: 99999;
                pointer-events: auto;
            }
            #chatbotWindow {
                position: fixed;
                right: 24px;
                bottom: 90px;
                width: 320px;
                max-height: 420px;
                background: #fff;
                border-radius: 16px;
                box-shadow: 0 12px 30px rgba(0,0,0,0.2);
                display: none;
                flex-direction: column;
                overflow: hidden;
                z-index: 99999;
                pointer-events: auto;
            }
            #chatbotHeader {
                background: #3b1a77;
                color: #fff;
                padding: 10px 14px;
                font-weight: 600;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            #chatbotMessages {
                padding: 12px;
                overflow-y: auto;
                flex: 1;
                font-size: 14px;
            }
            .chatbot-msg {
                margin-bottom: 10px;
                line-height: 1.4;
            }
            .chatbot-msg.user {
                text-align: right;
                color: #3b1a77;
                font-weight: 600;
            }
            .chatbot-msg.bot {
                text-align: left;
                color: #333;
            }
            #chatbotInputArea {
                display: flex;
                gap: 6px;
                padding: 10px;
                border-top: 1px solid #eee;
                background: #fafafa;
            }
            #chatbotInput {
                flex: 1;
                border: 1px solid #ddd;
                border-radius: 10px;
                padding: 8px 10px;
                font-size: 14px;
            }
            #chatbotSend {
                background: #3b1a77;
                color: #fff;
                border: none;
                border-radius: 10px;
                padding: 8px 12px;
                font-size: 14px;
                pointer-events: auto;
            }
            #chatbotInput:disabled,
            #chatbotSend:disabled {
                opacity: 0.6;
                cursor: not-allowed;
            }
        </style>
    {% endblock %}
</head>

<body>
    <div id=\"preloader-active\">
        <div class=\"preloader d-flex align-items-center justify-content-center\">
            <div class=\"preloader-inner position-relative\">
                <div class=\"preloader-circle\"></div>
                <div class=\"preloader-img pere-text\">
                    <img src=\"{{ asset('front/img/logo/loder.png') }}\" alt=\"Chargement...\">
                </div>
            </div>
        </div>
    </div>
    
    <header>
        <div class=\"header-area header-transparent\">
            <div class=\"main-header \">
                <div class=\"header-bottom header-sticky\">
                    <div class=\"container-fluid\">
                        <div class=\"row align-items-center\">
                            <div class=\"col-xl-2 col-lg-2\">
                                <div class=\"logo\">
                                    <a href=\"{{ path('app_home') }}\"><img  src=\"{{ asset('front/img/logo/logo.png') }}\" style=\"width:100px;height:100px;\" alt=\"BacLab Logo\"></a>
                                </div>
                            </div>
                            <div class=\"col-xl-10 col-lg-10\">
                                <div class=\"menu-wrapper d-flex align-items-center justify-content-end\">
                                    <div class=\"main-menu d-none d-lg-block\">
                                        <nav>
                                            <ul id=\"navigation\">                                                                                          
                                                <li><a href=\"{{ path('app_home') }}\">Accueil</a></li>
                                                <li><a href=\"#\">Révision</a>
                                                    <ul class=\"submenu\">
                                                        <{# Dans ton menu navigation #}
<li><a href=\"{{ path('app_revision_filieres') }}\">Cours & PDF</a></li>
                                                        <li><a href=\"#\">Quiz & Tests</a></li>
                                                        <li><a href=\"{{ path('fiche_index') }}\">fiches & co-édition</a></li>
                                                    </ul>
                                                </li>
                                                <li><a href=\"#\">Orientation</a>
                                                    <ul class=\"submenu\">
                                                        <li><a href=\"#\">Calcul du Score</a></li>
                                                        <li><a href=\"#\">Guide des Filières</a></li>
                                                    </ul>
                                                </li>
                                                <li><a href=\"#\">Communauté</a></li>
                                                
                                                {% if is_granted('ROLE_USER') %}
                                                    <li class=\"button-header\"><a href=\"#\" class=\"btn btn3\">Mon Profil</a></li>
                                                {% else %}
                                                    <li class=\"button-header margin-left\"><a href=\"#\" class=\"btn\">S'inscrire</a></li>
                                                    <li class=\"button-header\"><a href=\"#\" class=\"btn btn3\">Connexion</a></li>
                                                {% endif %}
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                            </div> 
                            <div class=\"col-12\">
                                <div class=\"mobile_menu d-block d-lg-none\"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
        {% block body %}{% endblock %}
    </main>

    {% include 'components/chatbot.html.twig' %}

    <footer>
        <div class=\"footer-wrappper footer-bg\">
            <div class=\"footer-area footer-padding\">
                <div class=\"container\">
                    <div class=\"row justify-content-between\">
                        <div class=\"col-xl-4 col-lg-5 col-md-4 col-sm-6\">
                            <div class=\"single-footer-caption mb-50\">
                                <div class=\"footer-logo mb-25\">
                                    <a href=\"#\"><img src=\"{{ asset('front/img/logo/logo2_footer.png') }}\" alt=\"BacLab Footer\"></a>
                                </div>
                                <div class=\"footer-tittle\">
                                    <div class=\"footer-pera\">
                                        <p>Votre succès au Bac commence par une révision structurée et une orientation éclairée.</p>
                                    </div>
                                </div>
                                <div class=\"footer-social\">
                                    <a href=\"#\"><i class=\"fab fa-facebook-f\"></i></a>
                                    <a href=\"#\"><i class=\"fab fa-instagram\"></i></a>
                                    <a href=\"#\"><i class=\"fab fa-linkedin\"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class=\"col-xl-2 col-lg-3 col-md-4 col-sm-5\">
                            <div class=\"single-footer-caption mb-50\">
                                <div class=\"footer-tittle\">
                                    <h4>Révision</h4>
                                    <ul>
                                        <li><a href=\"#\">Mathématiques</a></li>
                                        <li><a href=\"#\">Sciences</a></li>
                                        <li><a href=\"#\">Informatique</a></li>
                                        <li><a href=\"#\">Économie</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class=\"col-xl-2 col-lg-4 col-md-4 col-sm-6\">
                            <div class=\"single-footer-caption mb-50\">
                                <div class=\"footer-tittle\">
                                    <h4>Orientation</h4>
                                    <ul>
                                        <li><a href=\"#\">Calculer mon Score</a></li>
                                        <li><a href=\"#\">Filières Universitaires</a></li>
                                        <li><a href=\"#\">Guide de l'Orientation</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class=\"footer-bottom-area\">
                <div class=\"container\">
                    <div class=\"footer-border\">
                        <div class=\"row d-flex align-items-center\">
                            <div class=\"col-xl-12 \">
                                <div class=\"footer-copy-right text-center\">
                                    <p>Copyright &copy; {{ \"now\"|date(\"Y\") }} BacLab - Tous droits réservés</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer> 

    {% block js %}
        <script src=\"{{ asset('front/js/vendor/modernizr-3.5.0.min.js') }}\"></script>
        <script src=\"{{ asset('front/js/vendor/jquery-1.12.4.min.js') }}\"></script>
        <script src=\"{{ asset('front/js/popper.min.js') }}\"></script>
        <script src=\"{{ asset('front/js/bootstrap.min.js') }}\"></script>
        <script src=\"{{ asset('front/js/jquery.slicknav.min.js') }}\"></script>
        <script src=\"{{ asset('front/js/owl.carousel.min.js') }}\"></script>
        <script src=\"{{ asset('front/js/slick.min.js') }}\"></script>
        <script src=\"{{ asset('front/js/wow.min.js') }}\"></script>
        <script src=\"{{ asset('front/js/animated.headline.js') }}\"></script>
        <script src=\"{{ asset('front/js/jquery.magnific-popup.js') }}\"></script>
        <script src=\"{{ asset('front/js/gijgo.min.js') }}\"></script>
        <script src=\"{{ asset('front/js/jquery.nice-select.min.js') }}\"></script>
        <script src=\"{{ asset('front/js/jquery.sticky.js') }}\"></script>
        <script src=\"{{ asset('front/js/jquery.barfiller.js') }}\"></script>
        <script src=\"{{ asset('front/js/jquery.counterup.min.js') }}\"></script>
        <script src=\"{{ asset('front/js/waypoints.min.js') }}\"></script>
        <script src=\"{{ asset('front/js/jquery.countdown.min.js') }}\"></script>
        <script src=\"{{ asset('front/js/hover-direction-snake.min.js') }}\"></script>
        <script src=\"{{ asset('front/js/contact.js') }}\"></script>
        <script src=\"{{ asset('front/js/jquery.form.js') }}\"></script>
        <script src=\"{{ asset('front/js/jquery.validate.min.js') }}\"></script>
        <script src=\"{{ asset('front/js/mail-script.js') }}\"></script>
        <script src=\"{{ asset('front/js/jquery.ajaxchimp.min.js') }}\"></script>
        <script src=\"{{ asset('front/js/plugins.js') }}\"></script>
        <script src=\"{{ asset('front/js/main.js') }}\"></script>
        <script>
            (function () {
                const btn = document.getElementById('chatbotBtn');
                const win = document.getElementById('chatbotWindow');
                const closeBtn = document.getElementById('chatbotClose');
                const input = document.getElementById('chatbotInput');
                const send = document.getElementById('chatbotSend');
                const msgs = document.getElementById('chatbotMessages');

                const addMsg = (text, who) => {
                    const div = document.createElement('div');
                    div.className = 'chatbot-msg ' + who;
                    div.textContent = text;
                    msgs.appendChild(div);
                    msgs.scrollTop = msgs.scrollHeight;
                };

                const sendMessage = async () => {
                    const text = (input.value || '').trim();
                    if (!text) return;
                    addMsg(text, 'user');
                    input.value = '';

                    try {
                        send.disabled = true;
                        input.disabled = true;
                        const res = await fetch('/chatbot/message', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ message: text })
                        });
                        let data = null;
                        try {
                            data = await res.json();
                        } catch (e) {
                            data = { message: 'Réponse invalide du serveur.' };
                        }
                        if (!res.ok) {
                            addMsg(data.message || 'Erreur serveur.', 'bot');
                        } else {
                            addMsg(data.message || 'Pas de réponse', 'bot');
                        }
                    } catch (e) {
                        addMsg('Erreur de connexion. Réessaie.', 'bot');
                    } finally {
                        send.disabled = false;
                        input.disabled = false;
                        input.focus();
                    }
                };

                btn.addEventListener('click', () => {
                    win.style.display = 'flex';
                    input.focus();
                });
                closeBtn.addEventListener('click', () => {
                    win.style.display = 'none';
                });
                send.addEventListener('click', sendMessage);
                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') sendMessage();
                });
            })();
        </script>
    {% endblock %}
</body>
</html>
", "base.html.twig", "C:\\Users\\Msi\\bac-lab\\templates\\base.html.twig");
    }
}
