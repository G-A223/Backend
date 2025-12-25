<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('email', 'Email'))
            ->add(TextFilter::new('firstName', 'Имя'))
            ->add(TextFilter::new('lastName', 'Фамилия'))
            ->add(TextFilter::new('phone', 'Телефон'))
            ->add(ChoiceFilter::new('roles', 'Роли')
                ->setChoices([
                    'Пользователь' => 'ROLE_USER',
                    'Администратор' => 'ROLE_ADMIN',
                    'Владелец' => 'ROLE_OWNER',
                    'Модератор' => 'ROLE_MODERATOR',
                ])
                ->canSelectMultiple());
    }
}
