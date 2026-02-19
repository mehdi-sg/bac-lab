<?php

namespace App\Repository;

use App\Entity\Program;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Program>
 */
class ProgramRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Program::class);
    }

    /**
     * Find programs by BAC type
     */
    public function findByBacType(?string $bacType): array
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.cutoff2024 IS NOT NULL')
            ->andWhere('p.cutoff2024 > 0');
            
        if ($bacType !== null) {
            $qb->andWhere('p.bacTypeAr = :bacType OR p.bacTypeAr IS NULL')
               ->setParameter('bacType', $bacType);
        }
        
        return $qb->orderBy('p.cutoff2024', 'ASC')
                  ->getQuery()
                  ->getResult();
    }

    /**
     * Find programs with filters
     */
    public function findWithFilters(array $filters = []): array
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.cutoff2024 IS NOT NULL')
            ->andWhere('p.cutoff2024 > 0');

        if (!empty($filters['bacType'])) {
            $qb->andWhere('p.bacTypeAr = :bacType OR p.bacTypeAr IS NULL')
               ->setParameter('bacType', $filters['bacType']);
        }

        if (!empty($filters['university'])) {
            $qb->andWhere('p.universityAr LIKE :university')
               ->setParameter('university', '%' . $filters['university'] . '%');
        }

        if (!empty($filters['domain'])) {
            $qb->andWhere('p.domainAr LIKE :domain')
               ->setParameter('domain', '%' . $filters['domain'] . '%');
        }

        if (!empty($filters['minCutoff'])) {
            $qb->andWhere('p.cutoff2024 >= :minCutoff')
               ->setParameter('minCutoff', $filters['minCutoff']);
        }

        if (!empty($filters['maxCutoff'])) {
            $qb->andWhere('p.cutoff2024 <= :maxCutoff')
               ->setParameter('maxCutoff', $filters['maxCutoff']);
        }

        $orderBy = $filters['orderBy'] ?? 'cutoff2024';
        $orderDirection = $filters['orderDirection'] ?? 'ASC';
        
        $qb->orderBy('p.' . $orderBy, $orderDirection);

        if (!empty($filters['limit'])) {
            $qb->setMaxResults($filters['limit']);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Get all unique universities (extracted from program names and institutions)
     */
    public function getUniversities(): array
    {
        // Get from institution_ar field
        $result1 = $this->createQueryBuilder('p')
            ->select('DISTINCT p.institutionAr as name')
            ->where('p.institutionAr IS NOT NULL')
            ->andWhere('p.institutionAr != :empty')
            ->setParameter('empty', '')
            ->getQuery()
            ->getScalarResult();

        // Get from program_name_ar field (extract university names in parentheses)
        $result2 = $this->createQueryBuilder('p')
            ->select('DISTINCT p.programNameAr as name')
            ->where('p.programNameAr LIKE :pattern')
            ->setParameter('pattern', '%(جامعة%')
            ->getQuery()
            ->getScalarResult();

        $universities = [];
        
        // Process institution names
        foreach ($result1 as $row) {
            $name = trim($row['name']);
            if (!empty($name)) {
                $universities[] = $name;
            }
        }
        
        // Extract university names from program names
        foreach ($result2 as $row) {
            $name = $row['name'];
            if (preg_match('/\(جامعة ([^)]+)\)/', $name, $matches)) {
                $universityName = 'جامعة ' . trim($matches[1]);
                if (!in_array($universityName, $universities)) {
                    $universities[] = $universityName;
                }
            }
        }
        
        // Remove duplicates and sort
        $universities = array_unique($universities);
        sort($universities);
        
        return array_values($universities);
    }

    /**
     * Get all unique domains (extracted from program names and domain field)
     */
    public function getDomains(): array
    {
        // Get from domain_ar field
        $result1 = $this->createQueryBuilder('p')
            ->select('DISTINCT p.domainAr as name')
            ->where('p.domainAr IS NOT NULL')
            ->andWhere('p.domainAr != :empty')
            ->setParameter('empty', '')
            ->getQuery()
            ->getScalarResult();

        // Get common domains from program names
        $result2 = $this->createQueryBuilder('p')
            ->select('DISTINCT p.programNameAr as name')
            ->where('p.programNameAr IS NOT NULL')
            ->getQuery()
            ->getScalarResult();

        $domains = [];
        
        // Process domain field
        foreach ($result1 as $row) {
            $name = trim($row['name']);
            if (!empty($name)) {
                $domains[] = $name;
            }
        }
        
        // Extract common domains from program names
        $commonDomains = [
            'الطب' => ['طب', 'الطب'],
            'الهندسة' => ['هندسة', 'الهندسة'],
            'العلوم' => ['علوم', 'العلوم'],
            'الآداب' => ['آداب', 'الآداب'],
            'الحقوق' => ['حقوق', 'الحقوق'],
            'الاقتصاد' => ['اقتصاد', 'إقتصاد'],
            'التربية' => ['تربية', 'التربية'],
            'الإعلامية' => ['إعلامية', 'الإعلامية'],
            'الرياضة' => ['رياضة', 'الرياضة']
        ];
        
        foreach ($result2 as $row) {
            $name = $row['name'];
            foreach ($commonDomains as $domain => $keywords) {
                foreach ($keywords as $keyword) {
                    if (strpos($name, $keyword) !== false && !in_array($domain, $domains)) {
                        $domains[] = $domain;
                        break 2;
                    }
                }
            }
        }
        
        // Remove duplicates and sort
        $domains = array_unique($domains);
        sort($domains);
        
        return array_values($domains);
    }

    /**
     * Get cutoff statistics
     */
    public function getCutoffStats(): array
    {
        $result = $this->createQueryBuilder('p')
            ->select('MIN(p.cutoff2024) as minCutoff, MAX(p.cutoff2024) as maxCutoff, AVG(p.cutoff2024) as avgCutoff')
            ->where('p.cutoff2024 IS NOT NULL')
            ->andWhere('p.cutoff2024 > 0')
            ->getQuery()
            ->getSingleResult();

        return [
            'min' => (float) $result['minCutoff'],
            'max' => (float) $result['maxCutoff'],
            'avg' => (float) $result['avgCutoff']
        ];
    }

    /**
     * Count programs by BAC type
     */
    public function countByBacType(): array
    {
        $result = $this->createQueryBuilder('p')
            ->select('p.bacTypeAr, COUNT(p.id) as count')
            ->where('p.cutoff2024 IS NOT NULL')
            ->andWhere('p.cutoff2024 > 0')
            ->groupBy('p.bacTypeAr')
            ->orderBy('count', 'DESC')
            ->getQuery()
            ->getResult();

        $counts = [];
        foreach ($result as $row) {
            $counts[$row['bacTypeAr'] ?? 'غير محدد'] = (int) $row['count'];
        }

        return $counts;
    }
}