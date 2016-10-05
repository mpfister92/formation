<?php
namespace OCFram;

class Router
{
    const NO_ROUTE = 1;
    protected $_routes = [];

    public function addRoute(Route $route)
    {
        if (!in_array($route, $this->_routes)) {
            //array_push($this->_routes,$route);
            $this->_routes[] = $route;
        }
    }

    public function getRoute($url)
    {
        //parcours de l'ensemble des routes du router
        foreach ($this->_routes as $route) {
            //si on a une correspondance
            if ($route->match($url)) {
                //on récupère dans $varsValue le retour de la fonction preg_match (le tableau $matches -> la chaine capturée)
                $varsValue = $route->match($url);
                //si la route a des variables
                if ($route->hasVars()) {
                    //on récupère dans $varsNames les noms des variables de la route
                    $varsNames = $route->varsNames();
                    //Création du tableau comportant les noms/variables des variables
                    $arrayVars = [];
                    //parcours de l'ensemble des variables de la route
                    foreach ($varsValue as $key => $value) {
                        if ($key !== 0) {
                            $arrayVars[$varsNames[$key - 1]] = $value;
                        }
                    }
                    $route->setVars($arrayVars);
                }
                return $route;
            }
        }
        //si on ne capture aucune chaine on lance une exception
        throw new \RuntimeException('Aucune route trouvée');
    }
}

?>