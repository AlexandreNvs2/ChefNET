<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\EventListener\IsGrantedAttributeListener;
class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    #On Restreint la route uniquement au User Ayant le Role Admin
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
return $this->render('admin/dashboard.html.twig');

    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Chef.NET - Administration')
            ->renderContentMaximized();

    }

    #[IsGranted('ROLE_ADMIN')]
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class);
       yield MenuItem::linkToCrud('Demandes de contact', 'fas fa-envelope', Contact::class);

    }
}
