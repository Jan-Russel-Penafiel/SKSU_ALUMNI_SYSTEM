ALTER TABLE payments
    MODIFY status ENUM('pending','paid','rejected','refunded') DEFAULT 'pending',
    MODIFY paid_at DATETIME DEFAULT NULL;

UPDATE payments
SET paid_at = NULL
WHERE status IN ('pending','rejected');
