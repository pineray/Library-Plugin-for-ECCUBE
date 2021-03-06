<?php

/*
 * This file is part of the Lib
 *
 * Copyright (C) 2017 pineray
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\Lib\Repository;

use Doctrine\ORM\EntityRepository;
use Eccube\Application;
use Plugin\Lib\Entity\Queue;

/**
 * QueueRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class QueueRepository extends EntityRepository
{
    /**
     * @var \Eccube\Application
     */
    protected $app;

    /**
     * @param \Eccube\Application $app
     */
    public function setApplication(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param string $name
     * @param array $data
     * @param int $time
     * @return bool|int
     */
    public function createItem($name, $data = [], $time = 0)
    {
        $em = $this->getEntityManager();
        $em->getConnection()->beginTransaction();
        try {
            $item = new Queue();
            $item->setName($name);
            $item->setData(serialize($data));
            $item->setTime($time);
            $item->setExpire(0);
            $item->setCreated(time());
            $em->persist($item);
            $em->flush();
            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            return false;
        }

        return $item->getItemId();
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        try {
            $types = $this->createQueryBuilder('q')
                ->select(['q.name, MAX(q.time) AS max_time'])
                ->groupBy('q.name')
                ->getQuery()
                ->getResult();
            return $types;
        }
        catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @param $name
     * @return array|bool
     */
    public function claimItem($name)
    {
        while (TRUE) {
            try {
                $qb = $this->createQueryBuilder('q')
                    ->where('q.expire = 0')
                    ->andWhere('q.name = :name')
                    ->setParameter('name', $name)
                    ->orderBy('q.created', 'ASC')
                    ->addOrderBy('q.item_id', 'ASC');
                $item = $qb->getQuery()->getSingleResult();
            }
            catch (\Exception $e) {
                return false;
            }
            if ($item) {
                try {
                    $em = $this->getEntityManager();
                    $em->getConnection()->beginTransaction();
                    $lease_time = $item->getTime();
                    $lease_time = ($lease_time) ?: 30;
                    $item->setExpire(time() + $lease_time);
                    $em->persist($item);
                    $em->flush();
                    $em->getConnection()->commit();

                    $data = unserialize(stream_get_contents($item->getData()));
                    $item->setData($data);
                    return $item;
                } catch (\Exception $e) {
                    return false;
                }
            }
            else {
                // No items currently available to claim.
                return false;
            }
        }
    }

    /**
     * @param \Plugin\Lib\Entity\Queue $item
     * @return bool
     * @throws \Exception
     */
    public function releaseItem($item)
    {
        $em = $this->getEntityManager();
        $em->getConnection()->beginTransaction();
        try {
            $item->setExpire(0);
            $em->persist($item);
            $em->flush();
            $em->getConnection()->commit();
            return true;
        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            throw $e;
        }
    }

    /**
     * @param \Plugin\Lib\Entity\Queue $item
     * @return bool
     * @throws \Exception
     */
    public function deleteItem($item)
    {
        $em = $this->getEntityManager();
        $em->getConnection()->beginTransaction();
        try {
            $em->remove($item);
            $em->flush();
            $em->getConnection()->commit();
            return true;
        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            throw $e;
        }
    }

    /**
     * @throws \Exception
     */
    public function garbageCollection()
    {
        try {
            // Clean up the queue for failed batches.
            $this->createQueryBuilder('q')
                ->delete('Queue', 'q')
                ->where('q.created < :created')
                ->setParameter('created', time() - 864000);

            // Reset expired items.
            $this->createQueryBuilder('q')
                ->update('Queue', 'q')
                ->set('q.expire', 0)
                ->where('q.expire <> 0')
                ->andWhere('q.expire < :expire')
                ->setParameter('expire', time());
        }
        catch (\Exception $e) {
            throw $e;
        }
    }
}
