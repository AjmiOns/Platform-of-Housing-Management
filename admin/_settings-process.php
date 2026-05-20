<?php
/**
 * Settings Form Processing
 * Handles POST requests and database updates for agency settings
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $data = [
        'agency_name'   => trim($_POST['agency_name']   ?? ''),
        'slogan'        => trim($_POST['slogan']         ?? ''),
        'email'         => trim($_POST['email']          ?? ''),
        'phone'         => trim($_POST['phone']          ?? ''),
        'whatsapp'      => trim($_POST['whatsapp']       ?? ''),
        'address'       => trim($_POST['address']        ?? ''),
        'city'          => trim($_POST['city']           ?? ''),
        'governorate'   => trim($_POST['governorate']    ?? ''),
        'map_embed_url' => trim($_POST['map_embed_url']  ?? ''),
        'facebook'      => trim($_POST['facebook']       ?? ''),
        'instagram'     => trim($_POST['instagram']      ?? ''),
        'working_hours' => trim($_POST['working_hours']  ?? ''),
    ];

    db()->prepare(
        'UPDATE agency_settings SET
            agency_name=:agency_name, slogan=:slogan, email=:email,
            phone=:phone, whatsapp=:whatsapp, address=:address,
            city=:city, governorate=:governorate, map_embed_url=:map_embed_url,
            facebook=:facebook, instagram=:instagram, working_hours=:working_hours
         WHERE id = 1'
    )->execute($data);

    flash('success', 'Paramètres modifiés avec succès.');
    redirect('admin/settings.php');
}
