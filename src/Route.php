<?php

namespace Gitiki;

use Silex\Route as BaseRoute;

class Route extends BaseRoute
{
    protected $queryRequirements = [];

    protected $conditionComputed = false;

    public function assertGet($variable, $regexp)
    {
        $this->queryRequirements[$variable] = 'request.query.has("'.$variable.'") and request.query.get("'.$variable.'") matches ("/^'.$regexp.'$/")';
        $this->conditionComputed = false;

        return $this;
    }

    public function getCondition()
    {
        if (!empty($this->queryRequirements) && false === $this->conditionComputed) {
            $this->setCondition(implode(' and ', $this->queryRequirements));
            $this->conditionComputed = true;
        }

        return parent::getCondition();
    }
}
