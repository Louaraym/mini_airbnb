<?php

namespace App\Controller\admin;

use App\Service\AppStats;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     * @param AppStats $appStats
     * @return Response
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function index(AppStats $appStats): Response
    {
        $stats = $appStats->getStats();
        $bestAdverts = $appStats->getAdvertStats('DESC');
        $worstAdverts = $appStats->getAdvertStats('ASC');

        return $this->render('admin/dashboard/index.html.twig', [
            'stats'     => $stats,
            'bestAdverts' => $bestAdverts,
            'worstAdverts' => $worstAdverts,
        ]);
    }
}
