<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "users")]
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
     * Champ non persisté, utilisé pour saisir un nouveau mot de passe
     */
    private ?string $plainPassword = null;

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $resetPasswordToken = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $resetPasswordTokenExpiresAt = null;


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photoProfil = null;

    // --- Infos perso ---
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $prenom = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(length: 12, nullable: true)]
    private ?string $codePostal = null;

    // --- Tailles ---
    #[ORM\Column(length: 10, nullable: true)]
    private ?string $tailleTshirt = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $taillePolo = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $taillePull = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $tailleSweat = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $tailleVeste = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $tailleShort = null;

    /* =======================================================
     * GETTERS / SETTERS
     * ======================================================= */

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
        return $this->email ?? '';
    }

    public function __toString(): string
    {
        $name = trim(($this->prenom ?? '') . ' ' . ($this->nom ?? ''));

        return $name !== '' ? $name : $this->getUserIdentifier();
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        if (!in_array('ROLE_USER', $roles, true)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Si tu stockes des données sensibles temporaires, nettoie-les ici
        $this->plainPassword = null;
    }

    public function getResetPasswordToken(): ?string
    {
        return $this->resetPasswordToken;
    }

    public function setResetPasswordToken(?string $resetPasswordToken): self
    {
        $this->resetPasswordToken = $resetPasswordToken;
        return $this;
    }

    public function getResetPasswordTokenExpiresAt(): ?\DateTimeImmutable
    {
        return $this->resetPasswordTokenExpiresAt;
    }

    public function setResetPasswordTokenExpiresAt(?\DateTimeImmutable $resetPasswordTokenExpiresAt): self
    {
        $this->resetPasswordTokenExpiresAt = $resetPasswordTokenExpiresAt;
        return $this;
    }

    public function isResetPasswordTokenValid(string $token): bool
    {
        return $this->resetPasswordToken !== null
            && hash_equals($this->resetPasswordToken, $token)
            && $this->resetPasswordTokenExpiresAt !== null
            && $this->resetPasswordTokenExpiresAt > new \DateTimeImmutable();
    }


    public function getPhotoProfil(): ?string
    {
        return $this->photoProfil;
    }

    public function setPhotoProfil(?string $photoProfil): self
    {
        $this->photoProfil = $photoProfil;
        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(?\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): self
    {
        $this->ville = $ville;
        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(?string $codePostal): self
    {
        $this->codePostal = $codePostal;
        return $this;
    }

    public function getTailleTshirt(): ?string
    {
        return $this->tailleTshirt;
    }

    public function setTailleTshirt(?string $tailleTshirt): self
    {
        $this->tailleTshirt = $tailleTshirt;
        return $this;
    }

    public function getTaillePolo(): ?string
    {
        return $this->taillePolo;
    }

    public function setTaillePolo(?string $taillePolo): self
    {
        $this->taillePolo = $taillePolo;
        return $this;
    }

    public function getTaillePull(): ?string
    {
        return $this->taillePull;
    }

    public function setTaillePull(?string $taillePull): self
    {
        $this->taillePull = $taillePull;
        return $this;
    }

    public function getTailleSweat(): ?string
    {
        return $this->tailleSweat;
    }

    public function setTailleSweat(?string $tailleSweat): self
    {
        $this->tailleSweat = $tailleSweat;
        return $this;
    }

    public function getTailleVeste(): ?string
    {
        return $this->tailleVeste;
    }

    public function setTailleVeste(?string $tailleVeste): self
    {
        $this->tailleVeste = $tailleVeste;
        return $this;
    }

    public function getTailleShort(): ?string
    {
        return $this->tailleShort;
    }

    public function setTailleShort(?string $tailleShort): self
    {
        $this->tailleShort = $tailleShort;
        return $this;
    }

    public function getCartageStatus(): string
    {
        return '';
    }
}
