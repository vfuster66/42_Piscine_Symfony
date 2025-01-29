<?php
// src/Entity/User.php
namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Vote;
use App\Entity\Post;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap(['user' => User::class, 'admin' => Admin::class])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: "author")]
    private Collection $posts;
    
    #[ORM\OneToMany(targetEntity: Vote::class, mappedBy: "user")]
    private Collection $votes;    

    public function __construct()
    {
        $this->roles = ['ROLE_USER'];
        $this->createdAt = new \DateTime();
        $this->posts = new ArrayCollection();
        $this->votes = new ArrayCollection();
    }    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function eraseCredentials(): void
    {

    }

    public function __toString(): string
    {
        return $this->username;
    }

    public function equals(User $user): bool
    {
        return $this->getId() === $user->getId();
    }

    public function isAdmin(): bool
    {
        return in_array('ROLE_ADMIN', $this->getRoles(), true);
    }

    public function getPosts(): Collection
    {
        return $this->posts;
    }
    
    public function getVotes(): Collection
    {
        return $this->votes;
    }
    
    public function getReputationScore(): int
    {
        $score = 0;
        foreach ($this->posts as $post) {
            foreach ($post->getVotes() as $vote) {
                $score += $vote->isLike() ? 1 : -1;
            }
        }
        return max(0, $score);
    }
    
    public function hasVotedOnPost(Post $post): ?Vote
    {
        foreach ($this->votes as $vote) {
            if ($vote->getPost() === $post) {
                return $vote;
            }
        }
        return null;
    }

    public function canCreatePost(): bool
    {
        return true;
    }

    public function canEditOwnPost(): bool
    {
        return true;
    }

    public function canLikePost(): bool
    {
        return $this->getReputationScore() >= 3 || $this->isAdmin();
    }

    public function canDislikePost(): bool
    {
        return $this->getReputationScore() >= 6 || $this->isAdmin();
    }

    public function canEditAnyPost(): bool
    {
        return $this->getReputationScore() >= 9 || $this->isAdmin();
    }
}