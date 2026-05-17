<?php
require_once __DIR__ . '/includes/functions.php';
// Détruire la session actuelle (déconnexion de l'utilisateur)
session_destroy();
// Redémarrer une nouvelle session
session_start();
flash('success', 'Vous etes deconnecte.');
// Rediriger l'utilisateur vers la page de connexion
redirect('login.php');
