<?php

namespace Api\Brewery;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Api\Beer\BeerEntity;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="breweries")
 */
class BreweryEntity
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
	 * @ORM\Column(type="text", length=5000)
	 * @var string
	 */
	protected $description;

	/**
	 * @ORM\Column(type="datetime")
	 * @var \DateTime
	 */
	protected $created;
	
	/**
	 * @ORM\OneToMany(targetEntity="Api\Beer\BeerEntity", mappedBy="brewery", cascade={"all"}, orphanRemoval=true, fetch="EXTRA_LAZY")
	 * @var ArrayCollection
	 **/
	protected $beers;
	
	public function __construct()
	{
		$this->created = new \DateTime('now');
		$this->beers = new ArrayCollection();
	}

	static public function loadValidatorMetadata(ClassMetadata $metadata)
	{
		$metadata->addPropertyConstraint('name', new Assert\NotBlank(['message' => 'E o nome da cervejaria?']));
		$metadata->addPropertyConstraint('description', new Assert\NotBlank(['message' => 'E a descrição da cervejaria?']));
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
	
	public function addBeer(BeerEntity $beer)
	{
		if (!$this->beers->contains($beer)) {
			$this->beers[] = $beer;
		}
	}
	
	public function removeBeer(BeerEntity $beer)
	{
		$this->beers->remove($beer);
	}
	
	public function getBeers()
	{
		return $this->beers;
	}
	
	public function setFromArray(array $data)
	{
		foreach ($data as $column => $value) {
			$this->$column = $value;
		}
		return $this;
	}
}
	