<?php
/**
 * user/logout.php — Déconnexion client
 */
require_once __DIR__ . '/../includes/user_auth.php';

logout_client();
flash('success', 'Vous avez été déconnecté avec succès.');
redirect('user/login.php');
