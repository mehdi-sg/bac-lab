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
        // line 28
        yield "</head>

<body>
    <div id=\"preloader-active\">
        <div class=\"preloader d-flex align-items-center justify-content-center\">
            <div class=\"preloader-inner position-relative\">
                <div class=\"preloader-circle\"></div>
                <div class=\"preloader-img pere-text\">
                    <img src=\"";
        // line 36
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
        // line 50
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
        // line 58
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_home");
        yield "\">Accueil</a></li>
                                                <li><a href=\"#\">Révision</a>
                                                    <ul class=\"submenu\">
                                                        <li><a href=\"#\">Cours & PDF</a></li>
                                                        <li><a href=\"#\">Quiz & Tests</a></li>
                                                        <li><a href=\"";
        // line 63
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
        // line 74
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 74, $this->source); })()), "user", [], "any", false, false, false, 74)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 75
            yield "                                                    <li class=\"button-header\"><a href=\"";
            yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_profile");
            yield "\" class=\"btn\">Mon Profil</a></li>
                                                    <li class=\"button-header\"><a href=\"";
            // line 76
            yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_logout");
            yield "\" class=\"btn btn3\">Déconnexion</a></li>
                                                ";
        } else {
            // line 78
            yield "                                                    <li class=\"button-header margin-left\"><a href=\"";
            yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_register");
            yield "\" class=\"btn\">S'inscrire</a></li>
                                                    <li class=\"button-header\"><a href=\"";
            // line 79
            yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_login");
            yield "\" class=\"btn btn3\">Connexion</a></li>
                                                ";
        }
        // line 81
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
        // line 97
        yield from $this->unwrap()->yieldBlock('body', $context, $blocks);
        // line 98
        yield "    </main>

    <footer>
        <div class=\"footer-wrappper footer-bg\">
            <div class=\"footer-area footer-padding\">
                <div class=\"container\">
                    <div class=\"row justify-content-between\">
                        <div class=\"col-xl-4 col-lg-5 col-md-4 col-sm-6\">
                            <div class=\"single-footer-caption mb-50\">
                                <div class=\"footer-logo mb-25\">
                                    <a href=\"#\"><img src=\"";
        // line 108
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
        // line 156
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
        // line 166
        yield from $this->unwrap()->yieldBlock('js', $context, $blocks);
        // line 193
        yield "</body>
</html>";
        
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
    ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 97
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

    // line 166
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

        // line 167
        yield "        <script src=\"";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/vendor/modernizr-3.5.0.min.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 168
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/vendor/jquery-1.12.4.min.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 169
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/popper.min.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 170
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/bootstrap.min.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 171
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/jquery.slicknav.min.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 172
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/owl.carousel.min.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 173
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/slick.min.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 174
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/wow.min.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 175
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/animated.headline.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 176
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/jquery.magnific-popup.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 177
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/gijgo.min.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 178
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/jquery.nice-select.min.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 179
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/jquery.sticky.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 180
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/jquery.barfiller.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 181
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/jquery.counterup.min.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 182
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/waypoints.min.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 183
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/jquery.countdown.min.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 184
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/hover-direction-snake.min.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 185
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/contact.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 186
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/jquery.form.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 187
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/jquery.validate.min.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 188
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/mail-script.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 189
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/jquery.ajaxchimp.min.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 190
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/plugins.js"), "html", null, true);
        yield "\"></script>
        <script src=\"";
        // line 191
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("front/js/main.js"), "html", null, true);
        yield "\"></script>
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
        return array (  501 => 191,  497 => 190,  493 => 189,  489 => 188,  485 => 187,  481 => 186,  477 => 185,  473 => 184,  469 => 183,  465 => 182,  461 => 181,  457 => 180,  453 => 179,  449 => 178,  445 => 177,  441 => 176,  437 => 175,  433 => 174,  429 => 173,  425 => 172,  421 => 171,  417 => 170,  413 => 169,  409 => 168,  404 => 167,  391 => 166,  369 => 97,  356 => 26,  352 => 25,  348 => 24,  344 => 23,  340 => 22,  336 => 21,  332 => 20,  328 => 19,  324 => 18,  320 => 17,  316 => 16,  312 => 15,  308 => 14,  303 => 13,  290 => 12,  267 => 6,  255 => 193,  253 => 166,  240 => 156,  189 => 108,  177 => 98,  175 => 97,  157 => 81,  152 => 79,  147 => 78,  142 => 76,  137 => 75,  135 => 74,  121 => 63,  113 => 58,  100 => 50,  83 => 36,  73 => 28,  71 => 12,  66 => 10,  59 => 6,  52 => 1,);
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
                                                        <li><a href=\"#\">Cours & PDF</a></li>
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
                                                
                                                {% if app.user %}
                                                    <li class=\"button-header\"><a href=\"{{ path('app_profile') }}\" class=\"btn\">Mon Profil</a></li>
                                                    <li class=\"button-header\"><a href=\"{{ path('app_logout') }}\" class=\"btn btn3\">Déconnexion</a></li>
                                                {% else %}
                                                    <li class=\"button-header margin-left\"><a href=\"{{ path('app_register') }}\" class=\"btn\">S'inscrire</a></li>
                                                    <li class=\"button-header\"><a href=\"{{ path('app_login') }}\" class=\"btn btn3\">Connexion</a></li>
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
    {% endblock %}
</body>
</html>", "base.html.twig", "C:\\Users\\Lenovo LOQ\\bac-lab\\templates\\base.html.twig");
    }
}
