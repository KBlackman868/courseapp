<?php

namespace App\Listeners;

use CodeGreenCreative\SamlIdp\Events\Assertion;
use LightSaml\Model\Assertion\Attribute;

class SamlAssertionAttributes
{
    public function handle(Assertion $event)
    {
        $user = auth()->user();

        if (!$user) {
            return;
        }

        // Add attributes that Moodle needs
        $event->attribute_statement
            ->addAttribute(new Attribute('uid', $user->id))
            ->addAttribute(new Attribute('email', $user->email))
            ->addAttribute(new Attribute('firstName', $user->first_name ?? ''))
            ->addAttribute(new Attribute('lastName', $user->last_name ?? ''))
            ->addAttribute(new Attribute('username', $user->email))
            ->addAttribute(new Attribute('department', $user->department ?? 'Ministry of Health'));
    }
}