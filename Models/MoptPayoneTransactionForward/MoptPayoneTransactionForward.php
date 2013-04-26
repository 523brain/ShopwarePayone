<?php

/**
 * $Id: $
 */

namespace Shopware\CustomModels\MoptPayoneTransactionForward;

use Shopware\Components\Model\ModelEntity,
    Doctrine\ORM\Mapping AS ORM,
    Symfony\Component\Validator\Constraints as Assert,
    Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Repository")
 * @ORM\Table(name="s_plugin_mopt_payone_transaction_forward")
 */
class MoptPayoneTransactionForward extends ModelEntity
{

  /**
   * @var integer $id
   * 
   * @ORM\Column(name="id", type="integer", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   */
  private $id;

  /**
   * @ORM\Column(name="status", type="string", length=100, precision=0, scale=0, nullable=false, unique=false)
   */
  private $status;


  /**
   * @ORM\Column(name="urls", type="array", precision=0, scale=0, nullable=false, unique=false)
   */
  private $urls;

  /**
   * @var \Doctrine\Common\Collections\ArrayCollection
   */
  private $transactionForwards;

  public function __construct()
  {
    $this->transactionForwards = new \Doctrine\Common\Collections\ArrayCollection();
  }

  /**
   * add transactionForward
   *
   * @param \Shopware\CustomModels\MoptPayoneTransactionForward\MoptPayoneTransactionForward $transactionForwards
   */
  public function addTransactionForward(\Shopware\CustomModels\MoptPayoneTransactionForward\MoptPayoneTransactionForward $transactionForward)
  {
    $this->transactionForwards[] = $transactionForward;
  }

  /**
   * Set transactionForwards
   *
   * @param $transactionForwards
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function setTransactionForwards($transactionForwards)
  {
    $this->transactionForwards = $transactionForwards;
    return $this;
  }

  /**
   * Get transactionForwards
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getTransactionForwards()
  {
    return $this->transactionForwards;
  }
  
  public function getId()
  {
    return $this->id;
  }

  public function setId($id)
  {
    $this->id = $id;
  }

  public function getStatus()
  {
    return $this->status;
  }

  public function setStatus($status)
  {
    $this->status = $status;
  }

  public function getUrls()
  {
    return $this->urls;
  }

  public function setUrls($urls)
  {
    $this->urls = $urls;
  }


}