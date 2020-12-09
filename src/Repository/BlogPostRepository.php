<?php

namespace App\Repository;

use App\Entity\BlogPost;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method BlogPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlogPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlogPost[]    findAll()
 * @method BlogPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogPost::class);
    }

	/**
	 * @return BlogPost[] Returns an array of BlogPost objects
	 */
	public function getLatestPaginated($page = 1, $limit = 10)
	{
		$offset =($page - 1) * $limit;
		$now = new DateTime("now");
		return $this->createQueryBuilder('b')
			->select()
            ->andWhere('b.publishAt < :now')
			->andWhere('b.isDraft = 0')
            ->setParameter('now', $now)
            ->orderBy('b.publishAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
		;
	}

	/**
	 * @return int Number of items in repository
	 */
	public function getCount()
	{
		$now = new DateTime("now");
		$result = $this->createQueryBuilder('b')
			->andWhere('b.publishAt < :now')
			->andWhere('b.isDraft = 0')
			->setParameter('now', $now)
			->select('count(b.id)')
			->getQuery()
			->getSingleResult();
		;
		$count = 0;
		foreach ($result as $c => $v) {
			$count = $v;
		}

		return $count;
	}

	/**
	 * @param DateTime $date
	 *
	 * @return BlogPost[] Returns an array of BlogPost objects
	 */
	public function getByMonth(DateTime $date)
	{
		try {
			$month = $date->format('m');
			$year = $date->format('Y');
			$date1 = new DateTime("$year/$month/1");
			$date2 = new DateTime("$year/$month/1");
			$date2->modify('+1 month');

			return $this->createQueryBuilder('b')
			            ->andWhere('b.publishAt >= :date1')
			            ->andWHere('b.publishAt < :date2')
			            ->setParameter('date1', $date1)
			            ->setParameter('date2', $date2)
			            ->orderBy('b.publishAt', 'DESC')
			            ->getQuery()
			            ->getResult()
				;
		} catch ( Exception $e ) {
			return null;
		}
	}

	/**
	 * @param DateTime $date
	 *
	 * @return BlogPost[] Returns an array of BlogPost objects
	 */
	public function getByYear(DateTime $date)
	{
		try {
			$year = $date->format('Y');
			$date1 = new DateTime("$year/01/01");
			$date2 = new DateTime("$year/01/01");
			$date2->modify('+1 year');

			return $this->createQueryBuilder('b')
			            ->andWhere('b.publishAt >= :date1')
			            ->andWHere('b.publishAt < :date2')
			            ->setParameter('date1', $date1)
			            ->setParameter('date2', $date2)
			            ->orderBy('b.publishAt', 'DESC')
			            ->getQuery()
			            ->getResult()
				;
		} catch ( Exception $e ) {
			return null;
		}
	}

    // /**
    //  * @return BlogPost[] Returns an array of BlogPost objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BlogPost
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
