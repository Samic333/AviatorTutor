<?php
declare(strict_types=1);
/** @var array $system */
/** @var array $mnemonics */
$h = static fn(?string $s): string => htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8');
$accent = $h((string)($system['color'] ?? '#38BDF8'));
?>
<style>
  .mn-head { margin-bottom: 18px; }
  .mn-head h1 { margin: 0 0 4px; font-size: 26px; font-weight: 700; }
  .mn-head p  { margin: 0; color: var(--thm-fg-muted, #94A3B8); font-size: 14px; }
  .mn-grid { display: grid; gap: 18px; grid-template-columns: 1fr; }
  @media (min-width: 800px) { .mn-grid { grid-template-columns: 1fr 1fr; } }
  .mn-card {
    padding: 22px; border-radius: 14px;
    background: var(--thm-card, rgba(255,255,255,0.04));
    border: 1px solid var(--thm-border, rgba(255,255,255,0.08));
  }
  .mn-phrase {
    font-family: 'JetBrains Mono', ui-monospace, Menlo, monospace;
    font-size: 32px; font-weight: 700;
    letter-spacing: 0.08em;
    color: <?= $accent ?>;
    margin: 0 0 14px;
  }
  .mn-break {
    list-style: none; margin: 0 0 14px; padding: 0;
    border-top: 1px solid var(--thm-border, rgba(255,255,255,0.08));
  }
  .mn-break li {
    display: grid; grid-template-columns: 36px 1fr; align-items: center; gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid var(--thm-border, rgba(255,255,255,0.08));
  }
  .mn-letter {
    font-weight: 700; font-size: 22px; text-align: center;
    color: <?= $accent ?>;
    font-family: 'JetBrains Mono', ui-monospace, Menlo, monospace;
  }
  .mn-meaning { font-size: 14px; line-height: 1.5; }
  .mn-section { margin: 12px 0 0; }
  .mn-section h3 {
    margin: 0 0 4px; font-size: 11.5px; font-weight: 700;
    letter-spacing: 0.06em; text-transform: uppercase;
    color: var(--thm-fg-muted, #94A3B8);
  }
  .mn-section p { margin: 0; font-size: 14px; line-height: 1.6; }
  .mn-empty {
    padding: 32px; text-align: center;
    color: var(--thm-fg-muted, #94A3B8);
    border: 1px dashed var(--thm-border, rgba(255,255,255,0.12));
    border-radius: 14px;
  }
  .mn-audio {
    margin-top: 10px;
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 10px; border-radius: 999px;
    background: <?= $accent ?>22; color: <?= $accent ?>;
    font-size: 12px; font-weight: 700; cursor: pointer; border: 0;
  }
</style>

<header class="mn-head">
  <h1>Mnemonics — <?= $h((string)$system['name']) ?></h1>
  <p>Memory hooks for <?= $h((string)$system['name']) ?>. Every mnemonic includes the letter breakdown, why it sticks, and a worked scenario.</p>
</header>

<?php if (empty($mnemonics)): ?>
  <p class="mn-empty">No mnemonics published yet for this system. Run the seed migration to populate the starter set, or add one via the admin panel.</p>
<?php else: ?>
  <div class="mn-grid">
    <?php foreach ($mnemonics as $m):
      $phrase   = (string) ($m['phrase'] ?? '');
      $break    = is_string($m['breakdown_json'] ?? null) ? json_decode((string)$m['breakdown_json'], true) : ($m['breakdown_json'] ?? []);
      if (!is_array($break)) $break = [];
      $why      = (string) ($m['why_it_works']   ?? '');
      $example  = (string) ($m['worked_example'] ?? '');
      $audio    = (string) ($m['audio_url']      ?? '');
    ?>
      <article class="mn-card">
        <div class="mn-phrase"><?= $h($phrase) ?></div>

        <?php if (!empty($break)): ?>
          <ul class="mn-break">
            <?php foreach ($break as $row): ?>
              <li>
                <span class="mn-letter"><?= $h((string)($row['letter'] ?? '')) ?></span>
                <span class="mn-meaning"><?= $h((string)($row['meaning'] ?? '')) ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>

        <?php if ($why !== ''): ?>
          <div class="mn-section">
            <h3>Why it works</h3>
            <p><?= $h($why) ?></p>
          </div>
        <?php endif; ?>

        <?php if ($example !== ''): ?>
          <div class="mn-section">
            <h3>Worked example</h3>
            <p><?= $h($example) ?></p>
          </div>
        <?php endif; ?>

        <?php if ($audio !== ''): ?>
          <button class="mn-audio" type="button" onclick="(function(u){var a=new Audio(u);a.play();})('<?= $h($audio) ?>');">
            ▶ Hear it
          </button>
        <?php endif; ?>
      </article>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
