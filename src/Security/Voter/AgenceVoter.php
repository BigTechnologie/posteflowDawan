<?php

namespace App\Security\Voter;

use App\Entity\Agence;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, Agence|null>
 */
class AgenceVoter extends Voter
{
    // constantes de permission
    public const LIST = 'AGENCE_LIST'; 
    public const VIEW = 'AGENCE_VIEW'; 
    public const CREATE = 'AGENCE_CREATE'; 
    public const EDIT = 'AGENCE_EDIT'; 
    public const DELETE = 'AGENCE_DELETE'; 

    // attributs supportés
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

    /**
     * Vérifie si l'attribut et le sujet sont supportés
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array(
            $attribute,
            self::SUPPORTED_ATTRIBUTES,
            true
        )) {
            return false;
        }

        /*
         * Ces actions concernent une agence précise.
         */
        if (in_array(
            $attribute,
            [
                self::VIEW,
                self::EDIT,
                self::DELETE,
            ],
            true
        )) {
            // Vérifie que le sujet est une instance d'Agence
            return $subject instanceof Agence;
        }

        /*
         * LIST et CREATE ne nécessitent pas
         * d’agence déjà existante.
         */
        return $subject === null; // Retourne true si le sujet est null (pas d'agence spécifique)
    }

    /**
     * Vérifie les permissions basées sur l'attribut et le sujet
     */
    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token
    ): bool {
        // On eécupère l'utilisateur connecté
        $utilisateur = $token->getUser(); 

        // Vérifie que l'utilisateur est une instance de User
        if (!$utilisateur instanceof User) {
            return false;
        }

        /*
         * L’administrateur possède tous les droits
         * sur les agences.
         */
        if ($this->isGranted(
            $token,
            User::ROLE_ADMIN
        )) {
            return true;
        }

        return match ($attribute) {
            /*
             * Tous les utilisateurs authentifiés
             * peuvent consulter les agences.
             */
            self::LIST,
            self::VIEW => $this->isGranted(
                $token,
                User::ROLE_USER
            ),

            /*
             * Création, modification et suppression
             * sont réservées aux administrateurs.
             */
            self::CREATE,
            self::EDIT,
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