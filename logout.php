<?php
require_once __DIR__ . '/includes/functions.php';
session_destroy();
session_start();
flash('success', 'Vous etes deconnecte.');
redirect('login.php');
