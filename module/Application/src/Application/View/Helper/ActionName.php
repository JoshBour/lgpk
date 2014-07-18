<?php
/**
 * User: Josh
 * Date: 12/9/2013
 * Time: 7:14 μμ
 */

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Mvc\Router\RouteMatch;

class ActionName extends AbstractHelper
{
    /**
     * @var RouteMatch
     */
    private $routeMatch;

    /**
     * Constructor for the action name view helper.
     *
     * @param RouteMatch $routeMatch
     */
    public function __construct($routeMatch)
    {
        $this->routeMatch = $routeMatch;
    }

    /**
     * Get the current action's name
     *
     * @return string|bool
     */
    public function __invoke()
    {
        if($this->routeMatch)
            return $this->routeMatch->getMatchedRouteName();
        return false;
    }

}