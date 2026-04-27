<div class="admin-import-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1>Import Tool</h1>
        <p class="subtitle">Upload PDFs and images to create flashcards and lessons</p>
    </div>

    <!-- Import Form -->
    <section class="card">
        <div class="card-header">
            <h2 class="card-title">Import Content</h2>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data" action="/admin/import/process" class="import-form" id="importForm">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                <div class="form-section">
                    <h3>Step 1: Select File</h3>
                    <div class="file-upload-area" id="fileUploadArea">
                        <i data-lucide="upload-cloud"></i>
                        <p>Drag and drop your file here or click to browse</p>
                        <small>Supported: PDF, PNG, JPG (max 50MB)</small>
                        <input type="file" name="file" id="fileInput" accept=".pdf,.png,.jpg,.jpeg" hidden>
                    </div>
                    <div id="fileName" class="file-selected" style="display: none;">
                        <span id="fileNameText"></span>
                        <button type="button" class="clear-file">
                            <i data-lucide="x"></i>
                        </button>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Step 2: Select System</h3>
                    <select name="system_id" class="form-control" required>
                        <option value="">Choose a system...</option>
                        <?php if (!empty($systems)): ?>
                            <?php foreach ($systems as $system): ?>
                                <option value="<?php echo htmlspecialchars($system['id']); ?>">
                                    <?php echo htmlspecialchars($system['name']); ?> (<?php echo htmlspecialchars($system['ata_code']); ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-section">
                    <h3>Step 3: Configure Import</h3>
                    <div class="form-group">
                        <label class="form-label">Content Type</label>
                        <div class="radio-group">
                            <label class="radio-item">
                                <input type="radio" name="content_type" value="flashcards" checked>
                                <span>Create Flashcards</span>
                            </label>
                            <label class="radio-item">
                                <input type="radio" name="content_type" value="lesson">
                                <span>Create Lesson</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Title/Name (optional)</label>
                        <input type="text" name="title" class="form-control" placeholder="Leave blank for auto-generated">
                    </div>

                    <div class="form-group">
                        <label class="form-control-checkbox">
                            <input type="checkbox" name="auto_create_cards" checked>
                            <span>Automatically create flashcards from content</span>
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                    <i data-lucide="upload"></i>
                    Import File
                </button>
            </form>

            <div class="import-instructions">
                <h3>How to Import Content</h3>
                <ol>
                    <li>Upload a PDF document or image file containing Q400 system information</li>
                    <li>Select the appropriate system for categorization</li>
                    <li>Choose whether to create flashcards or a lesson</li>
                    <li>Click Import and the system will process your content</li>
                </ol>
                <p class="hint">Tip: PDF documents work best for creating lessons, while images are ideal for flashcard pairs.</p>
            </div>
        </div>
    </section>

    <!-- Import History -->
    <?php if (!empty($importLogs)): ?>
    <section class="card">
        <div class="card-header">
            <h2 class="card-title">Import History</h2>
        </div>
        <div class="card-body">
            <div class="import-history-list">
                <?php foreach ($importLogs as $log): ?>
                    <div class="import-history-item">
                        <div class="import-header">
                            <div class="import-info">
                                <h4><?php echo htmlspecialchars($log['filename']); ?></h4>
                                <p class="import-meta">
                                    <span><?php echo htmlspecialchars(date('M j, Y H:i', strtotime($log['created_at']))); ?></span>
                                    <span class="separator">•</span>
                                    <span><?php echo htmlspecialchars($log['system_name'] ?? 'Unknown'); ?></span>
                                </p>
                            </div>
                            <div class="import-status">
                                <span class="status-badge <?php echo htmlspecialchars($log['status']); ?>">
                                    <?php echo htmlspecialchars(ucfirst($log['status'])); ?>
                                </span>
                            </div>
                        </div>
                        <div class="import-details">
                            <div class="detail-box">
                                <span class="detail-label">Records Created:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($log['records_imported'] ?? 0); ?></span>
                            </div>
                            <div class="detail-box">
                                <span class="detail-label">Type:</span>
                                <span class="detail-value"><?php echo htmlspecialchars(ucfirst($log['import_type'] ?? 'Unknown')); ?></span>
                            </div>
                            <?php if (!empty($log['error_message'])): ?>
                                <div class="detail-box error">
                                    <span class="detail-label">Error:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($log['error_message']); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
</div>

<style>
.admin-import-container {
    padding: 20px;
    max-width: 800px;
    margin: 0 auto;
}

.form-section {
    margin-bottom: 30px;
}

.form-section h3 {
    margin: 0 0 16px 0;
    font-size: 16px;
    font-weight: 600;
    color: var(--color-white-text);
}

.file-upload-area {
    border: 2px dashed rgba(59, 130, 246, 0.3);
    border-radius: 8px;
    padding: 40px 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    background: rgba(59, 130, 246, 0.05);
}

.file-upload-area:hover {
    border-color: var(--color-blue-accent);
    background: rgba(59, 130, 246, 0.1);
}

.file-upload-area i {
    font-size: 48px;
    color: var(--color-blue-accent);
    margin-bottom: 16px;
    display: block;
    width: 48px;
    height: 48px;
    margin-left: auto;
    margin-right: auto;
}

.file-upload-area p {
    margin: 0 0 8px 0;
    color: var(--color-white-text);
    font-weight: 500;
}

.file-upload-area small {
    display: block;
    color: var(--color-gray-text);
    font-size: 12px;
}

.file-selected {
    padding: 12px 16px;
    background: rgba(16, 185, 129, 0.1);
    border: 1px solid rgba(16, 185, 129, 0.3);
    border-radius: 6px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: var(--color-success);
    font-size: 14px;
}

.clear-file {
    background: transparent;
    border: none;
    color: var(--color-success);
    cursor: pointer;
    padding: 0;
    display: flex;
    align-items: center;
}

.clear-file i {
    width: 18px;
    height: 18px;
}

.form-group {
    margin-bottom: 16px;
}

.form-label {
    display: block;
    color: var(--color-white-text);
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 8px;
}

.form-control {
    width: 100%;
    padding: 10px 14px;
    background: var(--color-dark-bg);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 6px;
    color: var(--color-white-text);
    font-size: 14px;
    transition: border-color 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: var(--color-blue-accent);
}

.radio-group {
    display: flex;
    gap: 20px;
}

.radio-item {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.radio-item input[type="radio"] {
    cursor: pointer;
}

.radio-item span {
    color: var(--color-white-text);
    font-size: 14px;
}

.form-control-checkbox {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    padding: 10px 12px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 6px;
    transition: all 0.2s;
}

.form-control-checkbox:hover {
    background: rgba(59, 130, 246, 0.05);
}

.form-control-checkbox input[type="checkbox"] {
    cursor: pointer;
}

.form-control-checkbox span {
    color: var(--color-white-text);
    font-size: 14px;
}

.import-instructions {
    margin-top: 30px;
    padding: 20px;
    background: rgba(59, 130, 246, 0.05);
    border: 1px solid rgba(59, 130, 246, 0.2);
    border-radius: 8px;
}

.import-instructions h3 {
    margin: 0 0 16px 0;
    font-size: 16px;
    color: var(--color-white-text);
}

.import-instructions ol {
    margin: 0 0 16px 0;
    padding-left: 24px;
    color: var(--color-gray-text);
    line-height: 1.8;
}

.import-instructions li {
    margin-bottom: 8px;
}

.hint {
    margin: 0;
    color: var(--color-blue-accent);
    font-size: 13px;
    font-style: italic;
}

.import-history-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.import-history-item {
    padding: 16px;
    background: var(--color-dark-bg);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
}

.import-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
    gap: 16px;
}

.import-info h4 {
    margin: 0;
    color: var(--color-white-text);
}

.import-meta {
    margin: 6px 0 0 0;
    color: var(--color-gray-text);
    font-size: 12px;
}

.separator {
    margin: 0 8px;
}

.status-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    white-space: nowrap;
}

.status-badge.success {
    background: rgba(16, 185, 129, 0.2);
    color: var(--color-success);
}

.status-badge.error {
    background: rgba(239, 68, 68, 0.2);
    color: var(--color-danger);
}

.status-badge.pending {
    background: rgba(245, 158, 11, 0.2);
    color: var(--color-amber-warning);
}

.import-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 12px;
}

.detail-box {
    padding: 8px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 4px;
    font-size: 12px;
}

.detail-box.error {
    background: rgba(239, 68, 68, 0.1);
}

.detail-label {
    display: block;
    color: var(--color-gray-text);
    margin-bottom: 4px;
}

.detail-value {
    display: block;
    color: var(--color-white-text);
    font-weight: 600;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileUploadArea = document.getElementById('fileUploadArea');
    const fileInput = document.getElementById('fileInput');
    const fileName = document.getElementById('fileName');
    const fileNameText = document.getElementById('fileNameText');
    const clearFileBtn = document.querySelector('.clear-file');

    fileUploadArea.addEventListener('click', () => fileInput.click());

    fileUploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        fileUploadArea.style.borderColor = 'var(--color-blue-accent)';
        fileUploadArea.style.background = 'rgba(59, 130, 246, 0.15)';
    });

    fileUploadArea.addEventListener('dragleave', () => {
        fileUploadArea.style.borderColor = 'rgba(59, 130, 246, 0.3)';
        fileUploadArea.style.background = 'rgba(59, 130, 246, 0.05)';
    });

    fileUploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        fileUploadArea.style.borderColor = 'rgba(59, 130, 246, 0.3)';
        fileUploadArea.style.background = 'rgba(59, 130, 246, 0.05)';

        if (e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files;
            updateFileName();
        }
    });

    fileInput.addEventListener('change', updateFileName);

    clearFileBtn.addEventListener('click', () => {
        fileInput.value = '';
        fileName.style.display = 'none';
        fileUploadArea.style.display = 'block';
    });

    function updateFileName() {
        if (fileInput.files.length > 0) {
            fileNameText.textContent = fileInput.files[0].name;
            fileName.style.display = 'flex';
            fileUploadArea.style.display = 'none';
        }
    }
});
</script>
