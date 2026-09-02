<?php

namespace App\Security\Voter;

use App\Entity\Client;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, Client|null>
 */
class ClientVoter extends Voter
{
    public const LIST = 'CLIENT_LIST';
    public const VIEW = 'CLIENT_VIEW';
    public const CREATE = 'CLIENT_CREATE';
    public const EDIT = 'CLIENT_EDIT';
    public const DELETE = 'CLIENT_DELETE';

    private const SUPPORTED_ATTRIBUTES = [
        self::LIST,
        self::VIEW,
        self::CREATE,
        self::EDIT,
        self::DELETE,
    ];

    public function __construct(
        private readonly AccessDecisionManagerInterface $accessDecisionManager
    ) {
    }

    protected function supports(
        string $attribute,
        mixed $subject
    ): bool {
        if (!in_array(
            $attribute,
            self::SUPPORTED_ATTRIBUTES,
            true
        )) {
            return false;
        }

        if (in_array(
            $attribute,
            [
                self::VIEW,
                self::EDIT,
                self::DELETE,
            ],
            true
        )) {
            return $subject instanceof Client;
        }

        return $subject === null;
    }

    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token
    ): bool {
        $utilisateur = $token->getUser();

        if (!$utilisateur instanceof User) {
            return false;
        }

        if ($this->isGranted(
            $token,
            User::ROLE_ADMIN
        )) {
            return true;
        }

        return match ($attribute) {
            self::LIST,
            self::VIEW => $this->isGranted(
                $token,
                User::ROLE_USER
            ),

            self::CREATE,
            self::EDIT => $this->isGranted(
                $token,
                User::ROLE_AGENT
            ),

            self::DELETE => false,

            default => false,
        };
    }

    private function isGranted(
        TokenInterface $token,
        string $role
    ): bool {
        return $this->accessDecisionManager->decide(
            $token,
            [$role]
        );
    }
}