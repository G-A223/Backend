<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\House;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

class HouseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return House::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('name', 'Название'))
            ->add(TextFilter::new('address', 'Адрес'))
            ->add(TextFilter::new('facilities', 'Удобства'))
            ->add(NumericFilter::new('beds', 'Кровати'))
            ->add(NumericFilter::new('bathrooms', 'Ванные комнаты'))
            ->add(NumericFilter::new('price', 'Цена'))
            ->add(NumericFilter::new('available', 'Доступно мест'));
    }
}
