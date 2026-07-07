<?php
$file_name = $file['original_name'] ?? basename($file['file_url']);
$extension = $file['file_extension'] ?? pathinfo($file['file_url'], PATHINFO_EXTENSION);
$size = !empty($file['size']) ? formatFileSize($file['size']) : '';
?>
<div class="file-item <?= getFileIconClass($extension) ?>">
    <div class="file-icon <?= getFileIconClass($extension) ?>">
        <?= getFileEmoji($extension) ?>
    </div>

    <div class="file-info">
        <div class="file-header">
            <span class="file-name" title="<?= htmlspecialchars($file_name) ?>">
                <?= htmlspecialchars($file_name) ?>
            </span>
            <?php if (!empty($badge)): ?>
                <span class="file-badge"><?= $badge ?></span>
            <?php endif; ?>
        </div>

        <div class="file-meta">
            <?php if ($size): ?>
                <span class="file-meta-item">
                    <span class="file-meta-icon">📦</span>
                    <?= $size ?>
                </span>
            <?php endif; ?>

            <span class="file-meta-item">
                <span class="file-meta-icon">📄</span>
                .<?= strtoupper($extension) ?>
            </span>
        </div>
    </div>

    <div class="file-actions">
        <?php if (parse_url($_SERVER['REQUEST_URI'])['path'] == '/lesson/edit/'): ?>
            <form 
                action="<?= "/file/delete" ?>"
                method="POST"
                target="_blank"
                class="file-action file-action--preview delete-button"
                title="Просмотр">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="file_id" value="<?= $file['id'] ?>">
                <button type="submit"> ❌ </button>
            </form>
        <?php endif; ?>
        <?php if (canPreviewFile($extension)): ?>
            <a href="<?= htmlspecialchars($file['file_url']) ?>"
                id="<?= $file['id'] ?>"
                target="_blank"
                class="file-action file-action--preview"
                title="Просмотр">
                👁️
            </a>
        <?php endif; ?>
        <a href="<?= htmlspecialchars($file['file_url']) ?>"
            download="<?= htmlspecialchars($file_name) ?>"
            class="file-action file-action--download"
            title="Скачать">
            📥
        </a>
    </div>
</div>