<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\CreateCourierFileAction;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\CourierFileRepository;
use App\Controller\CreateMultiCourierFileAction;
use App\Controller\ProcessOcrCourierFileAction;
use App\Filter\OrderByListFilter;

/**
 * @ORM\Entity
 * @ApiResource()
 * @ApiFilter(OrderByListFilter::class)
 * @ORM\Entity(repositoryClass=CourierFileRepository::class)
 */
class CourierFile
{
    const DOCUMENT = 'DOCUMENT';
    const ANSWER_LEVEL_1 = 'ANSWER_LEVEL_1';
    const ANSWER_LEVEL_2 = 'ANSWER_LEVEL_2';
    const ANSWER_DEFINITIVE = 'ANSWER_DEFINITIVE';

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @ORM\Id
     */
    protected $id;

    /**
     * @var string|null
     *
     * @ORM\Column(nullable=false)
     */
    public $filePath;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\Choice(choices={
     *     self::DOCUMENT,
     *     self::ANSWER_LEVEL_1,
     *     self::ANSWER_LEVEL_2,
     *     self::ANSWER_DEFINITIVE,
     *     },
     *     message="Le type doit être self::DOCUMENT,
     *     self::ANSWER_LEVEL_1,
     *     self::ANSWER_LEVEL_2,
     *     self::ANSWER_DEFINITIVE"
     * )
     */
    private $type;

    /**
     * @Groups({"courier:read", "courier_file:read", "courier_file:write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    public function __construct()
    {
        $this->type = self::DOCUMENT;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
