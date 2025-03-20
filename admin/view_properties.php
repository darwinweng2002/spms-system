<?php
session_start();
require_once 'db.php';

$records = $pdo->query("SELECT * FROM property_accountabilities ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<table>
    <thead>
        <tr>
            <th>Reference No.</th>
            <th>Accountable Officer</th>
            <th>Article</th>
            <th>Quantity</th>
            <th>Unit</th>
            <th>Unit Cost</th>
            <th>Total Cost</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($records as $record): ?>
            <tr>
                <td><?= htmlspecialchars($record['reference_no']) ?></td>
                <td><?= htmlspecialchars($record['accountable_officer']) ?></td>
                <td><?= htmlspecialchars($record['article']) ?></td>
                <td><?= htmlspecialchars($record['quantity']) ?></td>
                <td><?= htmlspecialchars($record['unit']) ?></td>
                <td><?= htmlspecialchars($record['unit_cost']) ?></td>
                <td><?= htmlspecialchars($record['total_cost']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
