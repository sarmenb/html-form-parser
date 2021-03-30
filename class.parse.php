<?php
/*	HTML Form Parser
 * 
 *	Author: Sarmen Boyadjian
 *	http://www.sarmenb.com
 *
 */


class ParseHtml
{
	public $source;

	/**
	* Parse and list all label fields of a form
	*
	*/
	public function getLabels()
	{
		$content = preg_replace("/&(?!(?:apos|quot|[gl]t|amp);|#)/", '&amp;', $this->source);

		$doc = new DOMDocument();
		@$doc->loadHTML($content);
		$xpath = new DomXPath($doc);
		$items = $xpath->query('//form//label');
		$labels = array();

		foreach($items as $item)
		{
			$node = str_replace('*', '', $item->nodeValue);
			$node = trim($node);
			array_push($labels, $node);
		}
		
		return $labels;
	}


	/**
	* Fetch and Parse a form for input fields
	*
	*/
	public function getInputs($type='text')
	{
		$content = preg_replace("/&(?!(?:apos|quot|[gl]t|amp);|#)/", '&amp;', $this->source);

		$doc = new DOMDocument();
		@$doc->loadHTML($content);
		$xpath = new DomXPath($doc);
		$list = array();

		if($type == 'text')
		{
			$items = $xpath->query('//form//input | //form//select | //form//textarea');
			foreach($items as $item)
			{
				if($item->getAttribute('type') != 'submit' AND $item->getAttribute('name') != 'Submit-button')
				{
					if($item->getAttribute('type') != 'hidden')
					{
						$field = str_replace('[]', '', $this->has_attribute($item, 'name'));
						$field = trim($field);

						array_push($list, $field);
					}
				}
			}
		}
		elseif($type == 'hidden')
		{
			$items = $xpath->query('//input | //select');
			foreach($items as $item)
			{
				if($item->getAttribute('type') == 'hidden')
				{
					array_push($list, array(
						'name' => trim($this->has_attribute($item, 'name')),
						'value' => trim($this->has_attribute($item, 'value'))
					));
				}
			}
		}

		return $list;
	}

	/**
	* Checks if the dom being parsed has an attribute
	*
	*/
	public function has_attribute($obj, $attribute)
	{
		if($obj->hasAttribute($attribute))
		{
			return $obj->getAttribute($attribute);
		}
		else
		{
			return '';
		}
	}

	function field_mapping($label, $input)
	{
		return array(
			'label' => $label,
			'input' => $input
		);
	}

	/**
	* gets all values and puts it together and gives it
	*
	*/
	public function getfields()
	{
		$labels = $this->getLabels();
		$inputs = $this->getInputs();
		$hidden_inputs = $this->getInputs('hidden');

		$fields = array_map(array($this, 'field_mapping'), $labels, $inputs);

		return array(
			'fields' => $fields,
			'hidden' => $hidden_inputs
		);
	}










}