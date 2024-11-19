<?php

namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Doctrine\ORM\Mapping as ORM;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
class Employee implements UserInterface, TwoFactorInterface, PasswordAuthenticatedUserInterface, GoogleAuthenticatorInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    
    
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(groups: ["add_employee"])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(groups: ["add_employee"])]
    private ?string $last_name = null;

    #[ORM\Column(length: 255)]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(length: 500)]
    #[SecurityAssert\UserPassword]
    private ?string $password = null;

    #[ORM\Column(type: "json")]
    private array $roles = [];

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $role = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $contract = null;

    #[ORM\Column(nullable: true)]
    private ?bool $active = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private bool $googleAuthenticatorEnabled = true;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $googleAuthenticatorSecret = null;

    #[ORM\Column]
    #[Assert\Type(\DateTimeImmutable::class)]
    private ?\DateTimeImmutable $arrival_date = null;

    /**
     * @var Collection<int, Project>
     */
    #[ORM\ManyToMany(targetEntity: Project::class, inversedBy: 'employees')]
    private Collection $projects;

    /**
     * @var Collection<int, Task>
     */
    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'employees')]
    private Collection $tasks;

    #[ORM\ManyToOne(inversedBy: 'employees')]
    #[ORM\JoinColumn(nullable: true)]
    private ?TimeEntry $timeEntry = null;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
        $this->tasks = new ArrayCollection();
        
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): static
    {
        $this->last_name = $last_name;

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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    ///////////////////////////////

    public function getTwoFactorSecret(): string
    {
        // Return the secret key used for 2FA
        return $this->getGoogleAuthenticatorSecret();
    }

    public function setTwoFactorSecret(string $secret): self
    {
        $this->setGoogleAuthenticatorSecret($secret);
        return $this;
    }
//
public function checkCode(TwoFactorInterface $user, string $code): bool
{
   
    $googleAuthenticator = new \Google\Authenticator\GoogleAuthenticator();
    return $googleAuthenticator->checkCode($user->getGoogleAuthenticatorSecret(), $code);
}

public function getQRContent(TwoFactorInterface $user): string
{
    
    return \Google\Authenticator\GoogleAuthenticator::getQRCodeGoogleUrl(
        'TaskLinker',
        $user->getGoogleAuthenticatorSecret(),
        'TaskLinker'
    );
}

public function generateSecret(): string
{
    $googleAuthenticator = new \Google\Authenticator\GoogleAuthenticator();
    $secret = $googleAuthenticator->generateSecret();
    $this->googleAuthenticatorSecret = $secret;

    return $secret;
}
//
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }


    public function eraseCredentials(): void
    {
        // $this->plainPassword = null;
    }


    public function isAdmin(): bool 
    {
        return in_array('ROLE_ADMIN', $this->roles);
    }

    public function setAdmin(bool $admin): static 
    {
        $this->roles = $admin ? ['ROLE_ADMIN'] : [];

        return $this;
    }

    public function getRole(): ?int
    {
        return $this->role;
    }

    public function setRole(int $role): static
    {
        $this->role = $role;

    
    $this->roles = match ($role) {
        0 => ['ROLE_USER'],
        1 => ['ROLE_ADMIN'],
        default => ['ROLE_USER'],
    };

        return $this;
    }

    public function getContract(): ?string
    {
        return $this->contract;
    }

    public function setContract(string $contract): static
    {
        $this->contract = $contract;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getArrivalDate(): ?\DateTimeImmutable
    {
        return $this->arrival_date;
    }

    public function setArrivalDate(\DateTimeImmutable $arrival_date): static
    {
        $this->arrival_date = $arrival_date;

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): static
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->addEmployee($this);
        }

        return $this;
    }

    public function removeProject(Project $project): static
    {
        $this->projects->removeElement($project);

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setEmployees($this);
        }

        return $this;
    }

    public function removeTask(Task $task): static
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getEmployees() === $this) {
                $task->setEmployees(null);
            }
        }

        return $this;
    }

    public function getTimeEntry(): ?TimeEntry
    {
        return $this->timeEntry;
    }

    public function setTimeEntry(?TimeEntry $timeEntry): static
    {
        $this->timeEntry = $timeEntry;

        return $this;
    }

    public function getGoogleAuthenticatorSecret(): ?string
    {
        return $this->googleAuthenticatorSecret;
    }

    public function setGoogleAuthenticatorSecret(?string $secret): void
    {
        $this->googleAuthenticatorSecret = $secret;
    }

    public function isGoogleAuthenticatorEnabled(): bool
    {
        return !empty($this->googleAuthenticatorSecret);
    }
 
    public function getGoogleAuthenticatorUsername(): string
    {
        return $this->email;
    } 

}
