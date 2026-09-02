<?php

namespace App\Security\Voter;

use App\Entity\Colis;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, Colis|null>
 */
class ColisVoter extends Voter
{
    // Constantes pour les attributs d'autorisation
    public const LIST = 'COLIS_LIST';
    public const VIEW = 'COLIS_VIEW';
    public const CREATE = 'COLIS_CREATE';
    public const EDIT = 'COLIS_EDIT';
    public const CHANGE_STATUS = 'COLIS_CHANGE_STATUS';
    public const DELETE = 'COLIS_DELETE';

    // Constantes pour les attributs d'autorisation supportés
    private const SUPPORTED_ATTRIBUTES = [
        self::LIST,
        self::VIEW,
        self::CREATE,
        self::EDIT,
        self::CHANGE_STATUS,
        self::DELETE,
    ];

    public function __construct(
        private readonly AccessDecisionManagerInterface $accessDecisionManager
    ) {
    }

    // Méthode qui vérifie si le voter peut traiter l'attribut et le sujet. $attribute = l'attribut d'autorisation, 
    // $subject = le sujet sur lequel on vérifie l'autorisation
    protected function supports(
        string $attribute,
        mixed $subject
    ): bool {
        /*
         * Le voter ne traite que les autorisations déclarées
         * dans SUPPORTED_ATTRIBUTES.
         */
        if (!in_array(
            $attribute,
            self::SUPPORTED_ATTRIBUTES,
            true
        )) {
            return false;
        }

        /*
         * Les actions VIEW, EDIT, CHANGE_STATUS et DELETE
         * doivent obligatoirement recevoir un objet Colis.
         */
        if (in_array(
            $attribute,
            [
                self::VIEW,
                self::EDIT,
                self::CHANGE_STATUS,
                self::DELETE,
            ],
            true
        )) {
            return $subject instanceof Colis;
        }

        /*
         * LIST et CREATE ne nécessitent pas encore
         * d’objet Colis existant.
         */
        return $subject === null;
    }

    // Méthode qui vérifie si l'utilisateur a le droit d'effectuer l'action
    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token
    ): bool {
        $utilisateur = $token->getUser();

        /*
         * Un utilisateur anonyme n’a aucun droit
         * sur la gestion des colis.
         */
        if (!$utilisateur instanceof User) {
            return false;
        }

        /*
         * L’administrateur possède tous les droits.
         *
         * Nous utilisons AccessDecisionManagerInterface pour
         * respecter une éventuelle hiérarchie des rôles définie
         * dans security.yaml.
         */
        if ($this->isGranted(
            $token,
            User::ROLE_ADMIN
        )) {
            return true;
        }

        // Match sur l'attribut pour déterminer le droit à accorder selon le rôle de l'utilisateur
        return match ($attribute) {
            /*
             * Tous les utilisateurs authentifiés possèdent
             * au minimum ROLE_USER grâce à User::getRoles().
             */
            self::LIST,
            self::VIEW => $this->isGranted( 
                $token,
                User::ROLE_USER
            ),

            /*
             * Les agents peuvent créer, modifier et faire
             * évoluer le statut des colis.
             */
            self::CREATE,
            self::EDIT,
            self::CHANGE_STATUS => $this->isGranted(
                $token,
                User::ROLE_AGENT
            ),

            /*
             * La suppression est réservée aux administrateurs.
             * Ceux-ci ont déjà été autorisés plus haut.
             */
            self::DELETE => false,

            default => false,
        };
    }

    // Méthode qui vérifie si l'utilisateur a le droit d'effectuer l'action
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