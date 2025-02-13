<?php

namespace Algorithm\C45;

class TreeNode
{
	/**
	 * self parent's TreeNode
	 * 
	 * @var Algorithm\C45\TreeNode
	 */
	protected $parent;
	
	private $id = [];

	/**
	 * attribute name
	 * 
	 * @var string
	 */
	protected $attribute;

	/**
	 * attribute's values
	 *  
	 * @var array
	 */
	protected $values;

	/**
	 * classes count for this node and it's child
	 * 
	 * @var array
	 */
	protected $classes_count;

	/**
	 * @var boolean
	 */
	protected $is_leaf;
    
    private function randomNumber($start, $end)
    {
        
        $number = rand($start, $end);
        if(in_array($number, $this->id))
        {
            return randomNumber($start, $end);
        }
        
        return $number;
    }
    
	/**
	 * Set parent
	 * 
	 * @param TreeNode $parent
	 */
	public function setParent(TreeNode $parent)
	{
		$this->parent = $parent;
	}

	/**
	 * Get parent
	 * 
	 * @return Algorithm\C45\TreeNode|null
	 */
	public function getParent()
	{
		return $this->parent;
	}

	/**
	 * Set attribute name
	 * 
	 * @param string $attribute
	 */
	public function setAttribute($attribute)
	{
		$this->attribute = $attribute;
	}

	/**
	 * Add classes count
	 * 
	 * @param string $valueName
	 * @param array  $classesCount
	 */
	public function addClassesCount($valueName, $classesCount = array())
	{
		$this->classes_count[$valueName] = $classesCount;
	}

	/**
	 * Set is leaf
	 * 
	 * @param boolean $is_leaf
	 * @return Algorithm\C45\TreeNode 
	 */
	public function setIsLeaf($is_leaf = false)
	{
		$this->is_leaf = $is_leaf;
		return $this;
	}

	/**
	 * Get is leaf
	 * 
	 * @return boolean
	 */
	public function getIsLeaf()
	{
		return $this->is_leaf;
	}

	/**
	 * Add child TreeNode
	 * 
	 * @param string $value
	 * @param mixed $child
	 */
	public function addChild($value, $child)
	{
		if (!isset($this->values))
		{
			$this->values = [];
		}

		$this->values[$value] = $child;
		return $this;
	}

	/**
	 * Get child
	 * 
	 * @param  string $value
	 * @return Algorithm\C45\TreeNode
	 */
	public function getChild($value)
	{
		if ($this->hasValue($value))
		{
			return $this->values[$value];
		}
	}

	/**
	 * Get values
	 * 
	 * @return array current of node value
	 */
	public function getValues()
	{
		return array_keys($this->values);
	}

	/**
	 * Get attribute name
	 * 
	 * @return string
	 */
	public function getAttributeName()
	{
		return $this->attribute;
	}

	/**
	 * Remove value from TreeNode
	 * 
	 * @param  string $value
	 * @return Algorithm\C45\TreeNode
	 */
	public function removeValue($value)
	{
		if ($this->hasValue($value))
		{
			unset($this->values[$value]);
		}

		return $this;
	}

	/**
	 * Check value in current node
	 * 
	 * @param  string  $value
	 * @return boolean
	 */
	public function hasValue($value)
	{
		if (!isset($this->values))
		{
			return false;
		}

		return array_key_exists($value, $this->values);
	}

	/**
	 * Classify data
	 * 
	 * @param  array  $data
	 * @return string
	 */
	public function classify(array $data)
	{
		if (isset($data[$this->attribute]))
		{
			$attrValue = $data[$this->attribute];

			if (!$this->hasValue($attrValue))
			{
				return 'unclassified';
			}

			$child = $this->values[$attrValue];

			if (!$child->getIsLeaf())
			{
				return $child->classify($data);
			}
			else
			{
				return $child->getChild('result');
			}
		}
	}

	/**
	 * 
	 * Draw tree with array
	 * 
	 * @return array
	 */
	public function toArray()
	{
		$data = [];
		$data['attribute'] = $this->attribute;
		$data['id'] = $this->randomNumber(1, 1000);
		foreach ($this->values as $key => $value)
		{
			if (!is_null($value) && !empty($key))
			{
				if ($value instanceof self)
				{
					$data['values'][$key] = $value->toArray();
				}
			}
		}

		return $data;
	}
	
	
	// untuk mempermudah membuat edges di library VisJS
	function getEdges($tree)
    {
        if(!empty($tree['from']) && !empty($tree['to']))
        {
            $results = [[
                "from" => $tree['from'],
                "to" => $tree['to'],
                "label" => $tree['line_text'],
            ]];
        }
        else
        {
            $results = [];
        }
        
        if(!empty($tree['child']))
        {
            foreach($tree['child'] as $child)
            {
                $results = array_merge($this->getEdges($child), $results);
            }
        }
        return $results;
    }
    
    // untuk mempermudah membuat nodes di library VisJS
    function getNodes($tree)
    {
        $results = [[
            "id" => $tree['id'],
            "label" => $tree['label'],
        ]];
        
        if(!empty($tree['child']))
        {
            foreach($tree['child'] as $child)
            {
                $results = array_merge($this->getNodes($child), $results);
            }
        }
        return $results;
    }
	
	/**
	 * 
	 * Draw tree with array with additional data. Good for visualize array with vis.js
	 * 
	 * @return array
	 */
	public function getTree($parent = null, $line_text = "", $nilai = 0)
	{
		$data = [];
		$data['label'] = $this->attribute;
		$data['id'] = $this->randomNumber(1, 1000);
		
		if(!is_null($parent))
		{
		    $data['from'] = $parent;
		    $data['to'] = $data['id'];
		}
		
		if(!empty($line_text))
		{
		    if(!empty($nilai))
    		{
    		   $data['label'] .= "\n".$nilai; 
    		}
    		
		    $data['line_text'] = $line_text;
		}
		
		if(!empty($this->values))
		{
		    $data['child'] = [];
    		foreach ($this->values as $key => $value)
    		{
    			if (!is_null($value) && !empty($key))
    			{
    				if ($value instanceof self)
    				{
    					$data['child'][] = $value->getTree($data['id'], $key, $this->getInstanceCountAsString($key));
    				}
    			}
    		}
		}

		return $data;
	}

	/**
	 * 
	 * Draw tree with array
	 * 
	 * @return array
	 */
	public function toJson()
	{
		$data = [];
		$data['attribute'] = $this->attribute;
		$data['id'] = $this->randomNumber(1, 1000);
		foreach ($this->values as $key => $value)
		{
			if (!is_null($value))
			{
				if ($value instanceof self)
				{
					$data['values'][$key] = $value->toJson();
				}
			}
		}

		return json_encode($data);
	}

	/**
	 * Draw tree with string
	 * 
	 * @param  string $tabs
	 * @return string
	 */
	public function toString($tabs = '')
	{
		$result = '';

		foreach ($this->values as $key => $child)
		{
			$result .= $tabs.$this->attribute.' = '.$key;

			if ($child->getIsLeaf())
			{
				$classCount = $this->getInstanceCountAsString($key);
				$result .= ' : '.$child->getChild('result').' '.$classCount."\n";
			}
			else 
			{
				$result .= "\n";
				$result .= $child->toString($tabs."|\t");
			}
		}

		return $result;
	}

	/**
	 * Get classes count as string
	 * 
	 * @param  string $attribute_value
	 * @return string
	 */
	private function getClassesCountAsString($attribute_value)
	{
		$result = '(';
		$total = array_sum($this->classes_count[$attribute_value]);

		foreach ($this->classes_count[$attribute_value] as $key => $value) {
			$result .= $value.'/';
		}

		$result .= $total.')';

		return $result;
	}

	/**
	 * Get instance count as string
	 * 
	 * @param  string $attribute_value string
	 * @return string
	 */
	private function getInstanceCountAsString($attribute_value)
	{
		$result = '(';
		$total = array_sum($this->classes_count[$attribute_value]);
		$child = $this->getChild($attribute_value);
		$className = $child->getChild('result');
		$classCount = $this->classes_count[$attribute_value][$className];

		if ($total > $classCount) {
			$result .= $total.'.0';
			$result .= '/'.($total - $classCount).'.0';
		} else {
			$result .= $classCount.'.0';
		}

		$result .= ')';

		return $result;
	}
}
