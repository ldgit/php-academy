<?php

namespace Adriatic\PHPAkademija\Test\TestDouble;

use Adriatic\PHPAkademija\SolidPrinciples\DependencyInversionPrinciple\DIPSolution\ReservationServiceImpl;
use Adriatic\PHPAkademija\SolidPrinciples\DependencyInversionPrinciple\DIPSolution\ProviderNotifier;
use PHPUnit\Framework\TestCase;

class DummyTest extends TestCase
{
    /** @test */
    public function canAcceptReservation()
    {
        // ProviderNotifier nam je potreban samo za bookAccommodation metodu, stoga za testiranje acceptReservation()
        // metode servisu šaljemo ProviderNotifierDummy
        $reservationService = new ReservationServiceImpl(new ProviderNotifierDummy());
        $this->assertEquals('Prihvaćamo zahtjev za rezervacijom "15" smještaja za korisnika',
            $reservationService->acceptReservation(15));
    }
}


class ProviderNotifierDummy implements ProviderNotifier
{
    public function askForConfirmation(int $accommodationId) {}
}
