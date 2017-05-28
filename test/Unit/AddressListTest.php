<?php
declare(strict_types=1);

namespace Genkgo\Mail\Unit;

use Genkgo\Mail\AbstractTestCase;
use Genkgo\Mail\Address;
use Genkgo\Mail\AddressList;
use Genkgo\Mail\EmailAddress;

final class AddressListTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function it_produces_the_correct_recipient_string_with_a_single_address()
    {
        $addressList = new AddressList([
            new Address(new EmailAddress('me1@example.com'), 'name'),
        ]);

        $this->assertEquals(
            "name <me1@example.com>",
            (string)$addressList
        );
    }

    /**
     * @test
     */
    public function it_produces_the_correct_recipient_string_with_multiple_addresses()
    {
        $addressList = new AddressList([
            new Address(new EmailAddress('me1@example.com'), 'name'),
            new Address(new EmailAddress('me2@example.com'), 'name'),
            new Address(new EmailAddress('me3@example.com'), 'name'),
        ]);

        $this->assertEquals(
            "name <me1@example.com>,name <me2@example.com>,name <me3@example.com>",
            (string)$addressList
        );
    }

    /**
     * @test
     */
    public function it_is_immutable()
    {
        $addressList = new AddressList([
            new Address(new EmailAddress('me1@example.com'), 'name'),
        ]);

        $this->assertNotSame($addressList, $addressList->withAddress(
            new Address(new EmailAddress('me1@example.com'), 'name'))
        );

        $this->assertNotSame($addressList, $addressList->withoutAddress(
            new Address(new EmailAddress('me1@example.com'), 'name'))
        );
    }

    /**
     * @test
     */
    public function it_can_add_an_address()
    {
        $addressList = new AddressList([
            new Address(new EmailAddress('me1@example.com'), 'name'),
        ]);

        $this->assertEquals(
            'name <me1@example.com>,name <me2@example.com>',
            (string) $addressList->withAddress(
                new Address(new EmailAddress('me2@example.com'), 'name')
            )
        );
    }

    /**
     * @test
     */
    public function it_can_remove_an_address()
    {
        $addressList = new AddressList([
            new Address(new EmailAddress('me1@example.com'), 'name'),
        ]);

        $this->assertEquals(
            '',
            (string) $addressList->withoutAddress(
                new Address(new EmailAddress('me1@example.com'), 'name')
            )
        );
    }
}