<?php

namespace App\Entity;

use App\Enum\typeContrat;
use App\Repository\EmployeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Tache;


#[ORM\Table(name: 'employe')]
#[ORM\Entity(repositoryClass: EmployeRepository::class)]
class Employe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $nom = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_entree = null;

    #[ORM\Column(length: 15, enumType: typeContrat::class, nullable: true)]
    private ?typeContrat $typeContrat = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Projet>
     */
    #[ORM\ManyToMany(targetEntity: Projet::class, inversedBy: 'employes')]
    #[ORM\JoinTable(name: 'employe_projet')]
    private Collection $projets;

    /**
     * @var Collection<int, tache>
     */
    #[ORM\OneToMany(mappedBy: 'employe', targetEntity: Taches::class, cascade: ['persist', 'remove'])]
    private Collection $taches;

    /**
     * Déclaration des Getter et Setter
     */

    public function __construct()
    {
        $this->projets = new ArrayCollection();
        $this->taches = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getDateEntree(): ?\DateTime
    {
        return $this->date_entree;
    }

    public function setDateEntree(\DateTime $date_entree): static
    {
        $this->date_entree = $date_entree;

        return $this;
    }

    public function getTypeContrat(): ?typeContrat
    {
        return $this->typeContrat;
    }

    public function setTypeContrat(?typeContrat $typeContrat): self
    {
        $this->typeContrat = $typeContrat;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    // Champ calculé (non persisté en base)
    public function getInitiales(): string
    {
        $initialeNom = $this->nom ? mb_substr($this->nom, 0, 1) : '';
        $initialePrenom = $this->prenom ? mb_substr($this->prenom, 0, 1) : '';

        return mb_strtoupper($initialeNom . $initialePrenom);
    }

    /**
     * Relation entre Employe et Projet
     * @return Collection<int, Projet>
     */
    public function getProjets(): Collection
    {
        return $this->projets;
    }

    public function addProjet(Projet $projet): static
    {
        if (!$this->projets->contains($projet)) {
            $this->projets->add($projet);
            $projet->addEmploye($this);
        }

        return $this;
    }

    public function removeProjet(Projet $projet): static
    {
        $this->projets->removeElement($projet);

        return $this;
    }

    /**
     * @return Collection<int, Taches>
     */
    public function getTaches(): Collection
    {
        return $this->taches;
    }

    public function addTaches(Taches $tache): static
    {
        if (!$this->taches->contains($tache)) {
            $this->taches->add($tache);
            $tache->setEmploye($this);
        }

        return $this;
    }

    public function removeTaches(Taches $tache): static
    {
        if ($this->taches->removeElement($tache)) {
            if ($tache->getEmploye() === $this) {
                $tache->setEmploye(null);
            }
        }

        return $this;
    }

}
