<h1><?php _e('OES Timeline Settings', 'oes'); ?></h1>
<p><?php _e('A collection of events can be visualised with a timeline. Each event must have at least one date ' .
        'field and can have a second date field to represent a time span. ' .
        'A specific date can additionally be supplemented by a date label.', 'oes'); ?></p>
<div class="oes-settings-nav-tabs-container">
    <ul class="oes-settings-nav-tabs"><?php
        foreach (['timeline' => 'General', 'timeline-categories' => 'Categories'] as $slug => $navTabLabel)
            printf('<li><a href="%s" class="%s">%s</a></li>',
                admin_url('admin.php?page=oes_timeline&select=' . $slug),
                ((isset($_GET['select']) || $slug !== 'timeline') ? '' : 'active'),
                $navTabLabel
            );
        ?>
    </ul>
</div>
<div class="oes-form-wrapper-small"><?php \OES\Admin\Tools\display_tool($_GET['select'] ?? 'timeline'); ?></div>