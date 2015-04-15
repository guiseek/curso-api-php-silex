<?php

namespace Api\Beer;

class BeerService
{
	private $em;
	private $entity = 'Api\beer\BeerEntity';

	public function __construct($em)
	{
		$this->em = $em;
	}

	public function __invoke()
	{
		return $this->em->getRepository($this->entity);
	}

	public function create(BeerEntity $entity)
	{
		try {
			$this->em->persist($entity);
			$this->em->flush();
			return $entity;
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
	public function update(BeerEntity $entity)
	{
		try {
			$this->em->merge($entity);
			$this->em->flush();
			return $entity;
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
	public function delete(BeerEntity $entity)
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