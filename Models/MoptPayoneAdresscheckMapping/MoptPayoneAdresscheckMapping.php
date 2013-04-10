<?php

/**
 * $Id: $
 */

namespace Shopware\CustomModels\MoptPayoneAdresscheckMapping;

use Shopware\Components\Model\ModelEntity,
    Doctrine\ORM\Mapping AS ORM,
    Symfony\Component\Validator\Constraints as Assert,
    Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="s_plugin_mopt_payone_adresscheck_mapping")
 */
class MoptPayoneAdresscheckMapping extends ModelEntity
{

  /**
   * @ORM\Column(name="id", type="integer", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   */
  private $id;

  /**
   * @ORM\Column(name="person_status", type="string", length=4, precision=0, scale=0, nullable=false, unique=true)
   */
  private $personStatus;

  /**
   * @ORM\Column(name="mapped_score", type="integer", precision=0, scale=0, nullable=false, unique=false)
   */
  private $mappedScore;

  /**
   * @var \Doctrine\Common\Collections\ArrayCollection
   */
  private $adressMappings;

  public function __construct()
  {
    $this->adressMappings = new \Doctrine\Common\Collections\ArrayCollection();
  }

  /**
   * add adressMapping
   *
   * @param \Shopware\CustomModels\MoptPayoneAdresscheckMapping\MoptPayoneAdresscheckMapping $adressMapping
   */
  public function addApiLog(\Shopware\CustomModels\MoptPayoneAdresscheckMapping\MoptPayoneAdresscheckMapping $adressMapping)
  {
    $this->adressMappings[] = $adressMapping;
  }

  /**
   * Set adressMappings
   *
   * @param $apiLogs
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function setAdressMappings($adressMappings)
  {
    $this->adressMappings = $adressMappings;
    return $this;
  }

  /**
   * Get adressMappings
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getAdressMappings()
  {
    return $this->adressMappings;
  }

}