<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_role('alumni');

$event_id = (int)($_GET['id'] ?? 0);
$uid = current_user_id();
if (!$event_id) { flash('error','Invalid event.'); redirect('pages/alumni/events.php'); }

$event = db_select_one($conn, "SELECT * FROM events WHERE id=?", 'i', [$event_id]);
if (!$event) { flash('error','Event not found.'); redirect('pages/alumni/events.php'); }

// Capacity check
if ((int)$event['capacity'] > 0) {
    $reg = db_count($conn,'event_registrations','event_id=?','i',[$event_id]);
    if ($reg >= (int)$event['capacity']) { flash('error','Event is full.'); redirect('pages/alumni/events.php'); }
}

$existing = db_select_one($conn, "SELECT id FROM event_registrations WHERE event_id=? AND user_id=?", 'ii', [$event_id, $uid]);
if ($existing) { flash('error','You are already registered.'); redirect('pages/alumni/events.php'); }

db_execute($conn, "INSERT INTO event_registrations (event_id, user_id) VALUES (?,?)", 'ii', [$event_id, $uid]);
flash('success', "Registered for: {$event['title']}");
redirect('pages/alumni/events.php');
