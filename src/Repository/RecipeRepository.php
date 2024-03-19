<?php

namespace App\Repository;

use App\Entity\Recipe;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 *
 * @method Recipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recipe[]    findAll()
 * @method Recipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    /**
     * Cette méthode nous permet de trouver les recettes publique
     * @param int $nbRecipes
     * @return array
     */
    public function findPublicRecipe(?int $nbRecipes): array
    {
        // Crée un QueryBuilder pour l'entité de recette
        $queryBuilder = $this->createQueryBuilder('r')
            // Ajoute une condition pour récupérer seulement les recettes publiques
            ->where('r.isPublic = 1')
            // Trie les recettes par date de création décroissante
            ->orderBy('r.createdAt', 'DESC');


        if ($nbRecipes !== 0 || $nbRecipes !== null) {
            $queryBuilder->setMaxResults($nbRecipes);
        }

        return $queryBuilder->getQuery()
            ->getResult();
    }


}
