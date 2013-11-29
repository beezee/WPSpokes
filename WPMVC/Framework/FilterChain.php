<?php

namespace WPMVC\Framework;

class FilterChain extends \WPMVC\Framework\Component
{
    private $_filters = array();
    
    public function filter($attributes, $filter)
    {
        if (!is_a($filter, '\WPMVC\Framework\Filter'))
            return;
        $this->_filters[] = array($attributes, $filter);
    }

    public function apply_to($context)
    {
        foreach($this->_filters as $filter)
            $filter[1]->apply($context, $filter[0]);
    }
}
