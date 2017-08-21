<?php

/*
 * This file is part of the Lib
 *
 * Copyright (C) 2017 pineray
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\Lib\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class LibConfigType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('cron_interval', 'choice',
            [
                'choices' => [
                    0 => 'plugin.lib.form.cron_interval.0',
                    3600 => 'plugin.lib.form.cron_interval.3600',
                    10800 => 'plugin.lib.form.cron_interval.10800',
                    21600 => 'plugin.lib.form.cron_interval.21600',
                    43200 => 'plugin.lib.form.cron_interval.43200',
                    86400 => 'plugin.lib.form.cron_interval.86400',
                    604800 => 'plugin.lib.form.cron_interval.604800'
                ]
            ]
        );
    }

    public function getName()
    {
        return 'lib_config';
    }

}
