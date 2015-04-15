<?php

namespace Api\Beer;

use Doctrine\ORM\Mapping as ORM;
use Api\Brewery\BreweryEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="beers")
 */
class BeerEntity
{
	/**
	 * @ORM\Id @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 * @var integer
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string", length=500)
	 * @var string
	 */
	protected $name;
	
	/**
	 * @ORM\Column(type="text")
	 * @var string
	 */
	protected $description;
	
	/**
	 * @ORM\Column(type="datetime")
	 * @var \DateTime
	 */
	protected $created;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Api\Brewery\BreweryEntity", inversedBy="beers",  cascade={"persist", "merge", "refresh"})
	 * @ORM\JoinColumn(name="brewery_id", referencedColumnName="id", onDelete="CASCADE")
	 **/
	protected $brewery;

	public function __construct()
	{
		$this->created = new \DateTime('now');
	}
	
	/**
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function setName($name)
	{
		return $this->name = $name;
	}

	public function getDescription()
	{
		return $this->description;
	}
	
	public function setDescription($description)
	{
		return $this->description = $description;
	}

	public function getCreated()
	{
		return $this->created;
	}
	
	public function getBrewery()
	{
		return $this->brewery;
	}
	
	public function addBrewery(BreweryEntity $entity)
	{
		return $this->brewery = $entity;
	}
	
	public function setFromArray(array $data)
	{
		foreach ($data as $column => $value) {
			$this->$column = $value;
		}
		return $this;
	}
}
	