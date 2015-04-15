<?php
namespace Api\Brewery;

class BreweryService
{
	private $em;
	private $entity = 'Api\Brewery\BreweryEntity';
	
	public function __construct($em)
	{
		$this->em = $em;
	}

	public function __invoke()
	{
		return $this->em->getRepository($this->entity);
	}
	
    public function create(BreweryEntity $entity)
    {
	   	try {
    		$this->em->persist($entity);
    		$this->em->flush();
    		return $entity;
    	} catch (Exception $e) {
    		echo $e->getMessage();
    	}
    }
    public function update(BreweryEntity $entity)
    {
    	try {
    		$this->em->merge($entity);
    		$this->em->flush();
    		return $entity;
    	} catch (Exception $e) {
    		echo $e->getMessage();
    	}
    }
	public function delete(BreweryEntity $entity)
	{
		try {
			$this->em->remove($entity);
			$this->em->flush();
			return $entity;
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
}