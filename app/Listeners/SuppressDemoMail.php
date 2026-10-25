<?php

namespace App\Listeners;

use App\Services\DemoProvisioner;
use Illuminate\Mail\Events\MessageSending;
use Symfony\Component\Mime\Address;

class SuppressDemoMail
{
    /**
     * Strip demo persona addresses from every outgoing message (workflow and risk
     * notifications fire for them like any other user) and cancel the send if no
     * real recipient is left. Returning false from MessageSending aborts the send.
     */
    public function handle(MessageSending $event): ?bool
    {
        $email = $event->message;
        $remaining = 0;

        foreach (['To', 'Cc', 'Bcc'] as $field) {
            $kept = array_filter(
                $email->{'get'.$field}(),
                fn (Address $address) => ! DemoProvisioner::isDemoEmail($address->getAddress())
            );

            $kept === []
                ? $email->getHeaders()->remove($field)
                : $email->{strtolower($field)}(...$kept);

            $remaining += count($kept);
        }

        return $remaining === 0 ? false : null;
    }
}
