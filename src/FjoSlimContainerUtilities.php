<?php

namespace fjourneau\SlimUtilities;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Fonctions utiles et communes pour Controllers et Middlewares
 *
 * @author fJourneau
 */
class FjoSlimContainerUtilities
{

    protected $container;

    /**
     * Fonction "magique" pour accéder directement à un 
     */
    public function __get($name)
    {
        return $this->container->get($name);
    }

    /**
     * Redirection utilisant les noms de route définis.
     * 
     * @param Response $response
     * @param string $name - Nom de la route
     * @param array $params - Tableau des params de la route : ['nom param' => 'val_param']
     * @param string $get_vars - (Optional) Variables GET à rajouter dans l'URL : debug=true&tab=auth
     */
    public function myRedirect(Response $response, string $name, array $params = [], string $get_vars = ""): Response
    {
        if ($get_vars) $get_vars = '?' . $get_vars;

        return $response->withRedirect($this->router->pathFor($name, $params) . $get_vars);
    }

    /**
     * Redirection utilisant un path (relatif à l'URL en cours)
     * 
     * @param Response $response
     * @param string $path - Path relatif à l'IRL en cours.
     * @return Response
     */
    public function redirectToLocation(Response $response, string $path): Response
    {
        return $response->withStatus(302)->withHeader('Location', $path);
    }

    /**
     * Ajout d'un message flash
     * 
     * @param string Nom variable Flash
     * @param mixed Valeur à passer
     */
    protected function addMessage(string $key, $value): void
    {
        $this->flash->addMessage($key, $value);
    }

    /**
     * Dump and die
     */
    public function dd($var)
    {
        if (is_array($var)) {
            echo "<pre>\n";
            print_r($var);
            echo "</pre>\n";
        } else {
            echo "<pre>\n";
            var_dump($var);
            echo "</pre>\n";
        }
        die();
    }

    /**
     * Retourne des infos que la route en cours
     * Si appelé dans Middleware, rajouter le paramètre suivant dans $seetings de l'App:
     * 'determineRouteBeforeAppMiddleware' => true
     */
    public function getRouteInfos(Request $request): ?array
    {
        $route = $request->getAttribute('route') ?? false;
        if ($route) {
            return [
                'name' => $route->getName(),
                // 'groups' => $route->getGroups(),  /* inutile (objet énorme) */
                'methods' => $route->getMethods(),
                'arguments' => $route->getArguments()
            ];
        }
    }

    /**
     * Retourne des infos liées à l'URL en cours
     * 
     * @param Request $request
     */
    public function getUriInfos(Request $request): ?array
    {
        $Uri = $request->getUri() ?? false;
        if ($Uri) {                                         /* URL exampole : https://localhost/PROJECT_NAME/admin/page?langu=FR#diese */
            return [
                'scheme' => $Uri->getScheme(),              /* http ou https */
                'host' => $Uri->getHost(),                  /* localhost */
                'baseUrl' => $Uri->getBaseUrl(),            /* https://localhost/PROJECT_NAME */
                'port' => $Uri->getPort(),                  /* Display only if specified in url */
                'path' => '/' . $Uri->getPath(),            /* /PROJECT_NAME/admin/page */
                'basePath' => $Uri->getBasePath(),          /* /PROJECT_NAME */
                'query' => $Uri->getQuery(),                /* langu=FR */
                'fullPathAndQuery' => $Uri->getBasePath() .
                    '/' . $Uri->getPath() .
                    ($Uri->getQuery() ? '?' . $Uri->getQuery() : '')
            ];
        }
    }
}