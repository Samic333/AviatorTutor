<?php declare(strict_types=1);
/** @var string $csrf_token */
/** @var ?string $flashOk */
/** @var ?string $flashError */

// Prefill subject + message when arriving from a pricing-tier waitlist CTA.
$tier = (string) ($_GET['tier'] ?? '');
$prefillSubject = '';
$prefillMessage = '';
switch ($tier) {
    case 'student':
        $prefillSubject = 'Waitlist — Student / Pilot Learner tier';
        $prefillMessage = "I'd like to be on the waitlist for the Student / Pilot Learner tier. Please email me when it goes live.\n\nMy current focus: ";
        break;
    case 'professional':
        $prefillSubject = 'Waitlist — Professional tier';
        $prefillMessage = "I'd like to be on the waitlist for the Professional tier. Please email me when it goes live.\n\nMy current focus: ";
        break;
    case 'instructor':
        $prefillSubject = 'Instructor / Organisation enquiry';
        $prefillMessage = "We're interested in multi-seat AviatorTutor licences for our team.\n\nOrganisation: \nApproximate number of seats: \nFleet / focus area: ";
        break;
}
?>
<section class="section">
    <div class="container container-tight">
        <span class="section__chip">Contact</span>
        <h1 style="font-size:clamp(1.8rem,3vw,2.4rem);">Get in touch</h1>
        <p>Support questions, feature requests, content corrections, organisation enquiries, waitlist signups &mdash; we read every message and reply within one business day.</p>

        <?php if (!empty($flashOk)): ?>
            <div class="flash flash--success"><?= htmlspecialchars($flashOk, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if (!empty($flashError)): ?>
            <div class="flash flash--error"><?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="post" action="/contact" class="card" style="margin-top:24px;">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-group">
                <label class="form-label" for="name">Name</label>
                <input class="form-input" type="text" id="name" name="name" required autocomplete="name">
            </div>
            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input class="form-input" type="email" id="email" name="email" required autocomplete="email">
            </div>
            <div class="form-group">
                <label class="form-label" for="subject">Subject</label>
                <input class="form-input" type="text" id="subject" name="subject" maxlength="200" placeholder="What's this about?" value="<?= htmlspecialchars($prefillSubject, ENT_QUOTES, 'UTF-8') ?>">
                <div class="form-help">Optional &mdash; helps us route your message to the right person.</div>
            </div>
            <div class="form-group">
                <label class="form-label" for="message">Message</label>
                <textarea class="form-textarea" id="message" name="message" rows="6" required minlength="10" style="font-family:inherit;"><?= htmlspecialchars($prefillMessage, ENT_QUOTES, 'UTF-8') ?></textarea>
                <div class="form-help">At least 10 characters. We don't share your email or message with anyone.</div>
            </div>
            <button type="submit" class="btn btn-primary">Send message</button>
        </form>
    </div>
</section>
