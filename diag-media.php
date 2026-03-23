<?php
/**
 * Media & WPML Diagnostic
 * URL: /wp-content/plugins/webhelpers/diag-media.php?token=mc_media_2026
 */

if (!isset($_GET['token']) || $_GET['token'] !== 'mc_media_2026') {
    die('Token requerido');
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');
if (!current_user_can('manage_options')) die('Sin permisos');

global $wpdb;

// 1. Check total attachments
$total_attachments = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'attachment'");

// 2. Check attachments with WPML data
$wpml_attachments = $wpdb->get_results("
    SELECT t.language_code, t.source_language_code, COUNT(*) as count 
    FROM {$wpdb->prefix}icl_translations t
    WHERE t.element_type = 'post_attachment'
    GROUP BY t.language_code, t.source_language_code
");

// 3. Sample of broken-looking items (no metadata or missing file)
$sample_attachments = $wpdb->get_results("
    SELECT p.ID, p.post_title, p.guid, pm.meta_value as file_path
    FROM {$wpdb->posts} p
    LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_wp_attached_file'
    WHERE p.post_type = 'attachment'
    LIMIT 20
");

?>
<!DOCTYPE html>
<html>
<head>
    <title>Media + WPML Diagnostic</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f0f2f5; }
        .card { background: white; padding: 20px; border-radius: 8px; shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
        .error { color: red; font-weight: bold; }
        .ok { color: green; }
        .thumb { width: 50px; height: 50px; object-fit: cover; background: #eee; }
    </style>
</head>
<body>
    <h1>🖼️ Media Library Diagnostic</h1>
    
    <div class="card">
        <h3>Resumen General</h3>
        <p>Total de adjuntos en <code>wp_posts</code>: <strong><?php echo $total_attachments; ?></strong></p>
    </div>

    <div class="card">
        <h3>Estado en WPML</h3>
        <table>
            <thead>
                <tr>
                    <th>Idioma</th>
                    <th>ID Original?</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($wpml_attachments as $row): ?>
                <tr>
                    <td><?php echo strtoupper($row->language_code); ?></td>
                    <td><?php echo $row->source_language_code ? 'Copia (' . $row->source_language_code . ')' : 'Original'; ?></td>
                    <td><?php echo $row->count; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h3>Muestra de 20 Adjuntos (Verificación de Archivo)</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Miniatura</th>
                    <th>Título</th>
                    <th>Ruta en BD</th>
                    <th>¿Existe en Disco?</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $upload_dir = wp_upload_dir();
                foreach ($sample_attachments as $att): 
                    $file = $upload_dir['basedir'] . '/' . $att->file_path;
                    $exists = (!empty($att->file_path) && file_exists($file));
                ?>
                <tr>
                    <td><?php echo $att->ID; ?></td>
                    <td>
                        <?php if ($exists): ?>
                            <img src="<?php echo wp_get_attachment_thumb_url($att->ID); ?>" class="thumb">
                        <?php else: ?>
                            <div class="thumb" style="display: flex; align-items: center; justify-content: center; font-weight: bold; color: #999;">X</div>
                        <?php endif; ?>
                    </td>
                    <td><?php echo esc_html($att->post_title); ?></td>
                    <td><code><?php echo esc_html($att->file_path); ?></code></td>
                    <td class="<?php echo $exists ? 'ok' : 'error'; ?>">
                        <?php echo $exists ? '✅ SÍ' : '❌ NO'; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
