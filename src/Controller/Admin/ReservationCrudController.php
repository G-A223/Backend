<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Reservation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

#[AdminRoute(path: '/reservation', name: 'reservation')]
class ReservationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Reservation::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
        ->add(TextFilter::new('comment', 'Комментарий'));
    }
}
