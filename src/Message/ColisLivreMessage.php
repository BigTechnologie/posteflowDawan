<?php 

namespace App\Message;

final class ColisLivreMessage
{
    public function __construct(
        private readonly int $colisId,
        private readonly string $numeroSuivi,
        private readonly ?string $clientEmail
    ){}

    public function getColisId(): int
    {
        return $this->colisId;
    }

    public function getNumeroSuivi(): string
    {
        return $this->numeroSuivi;
    }

    public function getClientEmail(): ?string
    {
        return $this->clientEmail;
    }

}