<?php

namespace App\Entity;

use App\Repository\ProgramRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProgramRepository::class)]
#[ORM\Table(name: 'programs')]
#[ORM\Index(columns: ['bac_type_ar'], name: 'idx_bac_type')]
#[ORM\Index(columns: ['cutoff_2024'], name: 'idx_cutoff')]
#[ORM\Index(columns: ['university_ar'], name: 'idx_university')]
class Program
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $domainAr = null;

    #[ORM\Column(length: 500)]
    private ?string $programNameAr = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $specializationAr = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $programCode = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $institutionAr = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $universityAr = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $bacTypeAr = null;

    #[ORM\Column(length: 200)]
    private ?string $formulaT = null;

    #[ORM\Column(name: 'cutoff_2024', type: 'decimal', precision: 8, scale: 3, nullable: true)]
    private ?string $cutoff2024 = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDomainAr(): ?string
    {
        return $this->domainAr;
    }

    public function setDomainAr(?string $domainAr): static
    {
        $this->domainAr = $domainAr;
        return $this;
    }

    public function getProgramNameAr(): ?string
    {
        return $this->programNameAr;
    }

    public function setProgramNameAr(?string $programNameAr): static
    {
        $this->programNameAr = $programNameAr;
        return $this;
    }

    public function getSpecializationAr(): ?string
    {
        return $this->specializationAr;
    }

    public function setSpecializationAr(?string $specializationAr): static
    {
        $this->specializationAr = $specializationAr;
        return $this;
    }

    public function getProgramCode(): ?string
    {
        return $this->programCode;
    }

    public function setProgramCode(?string $programCode): static
    {
        $this->programCode = $programCode;
        return $this;
    }

    public function getInstitutionAr(): ?string
    {
        return $this->institutionAr;
    }

    public function setInstitutionAr(?string $institutionAr): static
    {
        $this->institutionAr = $institutionAr;
        return $this;
    }

    public function getUniversityAr(): ?string
    {
        return $this->universityAr;
    }

    public function setUniversityAr(?string $universityAr): static
    {
        $this->universityAr = $universityAr;
        return $this;
    }

    public function getBacTypeAr(): ?string
    {
        return $this->bacTypeAr;
    }

    public function setBacTypeAr(?string $bacTypeAr): static
    {
        $this->bacTypeAr = $bacTypeAr;
        return $this;
    }

    public function getFormulaT(): ?string
    {
        return $this->formulaT;
    }

    public function setFormulaT(?string $formulaT): static
    {
        $this->formulaT = $formulaT;
        return $this;
    }

    public function getCutoff2024(): ?float
    {
        return $this->cutoff2024 ? (float) $this->cutoff2024 : null;
    }

    public function setCutoff2024(?float $cutoff2024): static
    {
        $this->cutoff2024 = $cutoff2024 ? (string) $cutoff2024 : null;
        return $this;
    }

    /**
     * Check if this program is valid (has required data)
     */
    public function isValid(): bool
    {
        // Relaxed validation - only check essential fields
        return !empty($this->programNameAr) 
            && !empty($this->formulaT) 
            && $this->cutoff2024 !== null
            && $this->cutoff2024 > 0;
    }

    /**
     * Get display name for the program
     */
    public function getDisplayName(): string
    {
        $parts = array_filter([
            $this->programNameAr,
            $this->specializationAr
        ]);
        
        return implode(' - ', $parts);
    }

    /**
     * Get full institution name
     */
    public function getFullInstitutionName(): string
    {
        $parts = array_filter([
            $this->institutionAr,
            $this->universityAr
        ]);
        
        return implode(' - ', $parts);
    }
}