<section class="admin-dashboard">
    <div class="admin-dashboard__intro">
        <div>
            <p class="admin-kicker">Website overview</p>
            <h1>Welcome back, <?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p>Review the current website activity and prepare content for the next update.</p>
        </div>
        <a class="admin-view-site" href="/" target="_blank" rel="noopener">View public website ↗</a>
    </div>

    <div class="admin-stats">
        <?php foreach ($stats as $stat): ?>
            <article class="admin-stat">
                <span><?= htmlspecialchars($stat['label'], ENT_QUOTES, 'UTF-8') ?></span>
                <strong><?= (int) $stat['value'] ?></strong>
            </article>
        <?php endforeach; ?>
    </div>

    <section class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <p class="admin-kicker">Client contact</p>
                <h2>Recent enquiries</h2>
            </div>
            <span><?= count($recentInquiries) ?> latest</span>
        </div>

        <?php if ($recentInquiries === []): ?>
            <p class="admin-empty">No enquiries have been received yet.</p>
        <?php else: ?>
            <div class="admin-table-wrap">
                <table>
                    <thead><tr><th>Name</th><th>Subject</th><th>Contact</th><th>Status</th><th>Received</th></tr></thead>
                    <tbody>
                    <?php foreach ($recentInquiries as $inquiry): ?>
                        <tr>
                            <td><?= htmlspecialchars($inquiry['name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($inquiry['subject'] ?: 'General enquiry', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($inquiry['email'] ?: 'Not provided', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><span class="admin-status"><?= htmlspecialchars($inquiry['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                            <td><?= htmlspecialchars((string) $inquiry['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</section>
