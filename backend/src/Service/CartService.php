<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    private const KEY_MERCH = 'cart_merch';
    private const KEY_TICKET = 'cart_ticket';
    private const KEY_MERCH_EMAIL = 'cart_merch_email';
    private const KEY_TICKET_EMAIL = 'cart_ticket_email';

    public function getMerchCart(SessionInterface $session): array
    {
        return (array) $session->get(self::KEY_MERCH, []);
    }

    public function setMerchCart(SessionInterface $session, array $cart): void
    {
        $session->set(self::KEY_MERCH, $cart);
    }

    public function addMerch(SessionInterface $session, int $id, string $size, int $qty = 1): void
    {
        $size = $size !== '' ? $size : 'TU';
        $key = $id . '|' . $size;
        $cart = $this->getMerchCart($session);
        $cart[$key] = [
            'id' => $id,
            'size' => $size,
            'quantity' => (int) (($cart[$key]['quantity'] ?? 0) + max(1, $qty)),
        ];
        $this->setMerchCart($session, $cart);
    }

    public function removeMerch(SessionInterface $session, string $key): void
    {
        $cart = $this->getMerchCart($session);
        unset($cart[$key]);
        $this->setMerchCart($session, $cart);
    }

    public function clearMerch(SessionInterface $session): void
    {
        $session->remove(self::KEY_MERCH);
        $session->remove(self::KEY_MERCH_EMAIL);
    }

    public function setMerchEmail(SessionInterface $session, ?string $email): void
    {
        if ($email) {
            $session->set(self::KEY_MERCH_EMAIL, $email);
        }
    }

    public function getMerchEmail(SessionInterface $session): ?string
    {
        return $session->get(self::KEY_MERCH_EMAIL);
    }

    public function getTicketCart(SessionInterface $session): array
    {
        return (array) $session->get(self::KEY_TICKET, []);
    }

    public function setTicketCart(SessionInterface $session, array $cart): void
    {
        $session->set(self::KEY_TICKET, $cart);
    }

    public function addTicket(SessionInterface $session, int $id, int $qty = 1): void
    {
        $key = (string) $id;
        $cart = $this->getTicketCart($session);
        $cart[$key] = [
            'id' => $id,
            'quantity' => (int) (($cart[$key]['quantity'] ?? 0) + max(1, $qty)),
        ];
        $this->setTicketCart($session, $cart);
    }

    public function removeTicket(SessionInterface $session, string $key): void
    {
        $cart = $this->getTicketCart($session);
        unset($cart[$key]);
        $this->setTicketCart($session, $cart);
    }

    public function clearTicket(SessionInterface $session): void
    {
        $session->remove(self::KEY_TICKET);
        $session->remove(self::KEY_TICKET_EMAIL);
    }

    public function setTicketEmail(SessionInterface $session, ?string $email): void
    {
        if ($email) {
            $session->set(self::KEY_TICKET_EMAIL, $email);
        }
    }

    public function getTicketEmail(SessionInterface $session): ?string
    {
        return $session->get(self::KEY_TICKET_EMAIL);
    }
}
