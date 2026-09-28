<?php

return [
    // Page titles and navigation
    'title' => 'Coupons Disponibles',
    'subtitle' => 'Économisez sur vos cours avec nos codes de réduction exclusifs',
    'special_offers' => 'Offres Spéciales',
    'my_coupons' => 'Mes Coupons',
    'available_coupons' => 'Coupons Disponibles',
    'coupon_code' => 'Code Promo',
    'apply_coupon' => 'Appliquer le Coupon',
    'apply_to_cart' => 'Appliquer au Panier',
    'remove_coupon' => 'Retirer le Coupon',
    'copy_code' => 'Copier le Code',
    'code_copied' => 'Code Copié !',
    'view_details' => 'Voir les Détails',
    'browse_courses' => 'Parcourir les Cours',
    'remaining' => 'restant',

    // Coupon types
    'discount' => 'Réduction',
    'percentage' => 'Pourcentage',
    'fixed_amount' => 'Montant Fixe',
    'off' => 'DE RÉDUCTION',

    // Validation messages - Success
    'valid' => 'Le coupon est valide et prêt à être utilisé !',
    'applied_successfully' => 'Coupon appliqué avec succès !',
    'removed_successfully' => 'Coupon retiré avec succès !',

    // Validation messages - Errors
    'code_not_found' => 'Code promo introuvable.',
    'cannot_be_used' => 'Le coupon ne peut pas être utilisé',
    'currently_inactive' => 'Ce coupon est actuellement inactif.',
    'not_yet_active' => 'Ce coupon n\'est pas encore actif. Il sera disponible à partir du :date.',
    'has_expired' => 'Ce coupon a expiré le :date.',
    'usage_limit_reached' => 'Ce coupon a atteint sa limite d\'utilisation maximale (:limit utilisations).',
    'user_limit_reached' => 'Vous avez déjà utilisé ce coupon le nombre maximum de fois (:limit).',
    'first_purchase_only' => 'Ce coupon est uniquement valable pour les nouveaux clients.',
    'customer_type_mismatch' => 'Ce coupon est uniquement pour les clients :coupon_type. Vous êtes un client :user_type.',
    'minimum_purchase_required' => 'Montant d\'achat minimum de :amount requis. Le total de votre panier est de :current.',
    'minimum_items_required' => 'Minimum :count articles requis dans le panier. Vous avez :current articles.',
    'not_applicable_courses' => 'Ce coupon n\'est pas applicable aux cours de votre panier.',

    // Customer types
    'new' => 'nouveau',
    'returning' => 'fidèle',
    'vip' => 'VIP',
    'all' => 'tous',

    // Coupon details
    'valid_until' => 'Valable Jusqu\'au',
    'valid_from' => 'Valable Dès',
    'expires_on' => 'Expire le',
    'minimum_purchase' => 'Achat Minimum',
    'minimum_items' => 'Articles Minimum',
    'usage_limit' => 'Limite d\'Utilisation',
    'per_user_limit' => 'Limite par Utilisateur',
    'customer_type' => 'Type de Client',
    'course_types' => 'Types de Cours',
    'stackable' => 'Cumulable',
    'auto_apply' => 'Application Auto',
    'public' => 'Public',

    // Messages
    'no_coupons_available' => 'Aucun coupon disponible pour le moment.',
    'check_back_later' => 'Veuillez revenir plus tard pour de nouvelles offres !',
    'enter_coupon_code' => 'Entrez le code promo',
    'terms_conditions' => 'Conditions Générales',
    'custom_message' => 'Message Personnalisé',
    'auto_apply_badge' => 'Application auto',
    'new_customers_only' => 'Nouveaux clients',
    'stackable_badge' => 'Cumulable',

    // Cart messages
    'coupon_discount' => 'Réduction Coupon',
    'coupon_applied' => 'Coupon Appliqué',
    'save_amount' => 'Vous économisez :amount',

    // Status
    'active' => 'Actif',
    'inactive' => 'Inactif',
    'expired' => 'Expiré',
    'upcoming' => 'À Venir',
];
