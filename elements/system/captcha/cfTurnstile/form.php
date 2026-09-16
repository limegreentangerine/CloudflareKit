<?php
defined('C5_EXECUTE') or die('Access denied.');

use Concrete\Core\Support\Facade\Application;

$app = Application::getFacadeApplication();
$form = $app->make('helper/form');
$config = $app->make('config');
?>

<div class="alert alert-info">
    <?php echo t('A site key and secret key must be provided. They can be obtained from the <a href="https://dash.cloudflare.com/" target="_blank">Cloudflare website</a>.'); ?>
</div>

<div class="form-group">
      <?php
        echo $form->label('site_key', t('Site Key'));
echo $form->text('site_key', $config->get('captcha.cfTurnstile.site_key'));
?>
</div>

<div class="form-group">
    <?php
    echo $form->label('secret_key', t('Secret Key'));
echo $form->text('secret_key', $config->get('captcha.cfTurnstile.secret_key'));
?>
</div>

<div class="form-group">
    <?php
    echo $form->label('theme', t('Theme'));
echo $form->select('theme', [
    '' => t('Select a theme'),
    'auto' => t('Auto'),
    'light' => t('Light'),
    'dark' => t('Dark'),
], $config->get('captcha.cfTurnstile.theme') ?? 'auto');
?>
    <div class="help-block">
        <p><?php echo t("Customize the widget's visual appearance to match your website's design."); ?></p>
        <ul>
            <li><code dir="auto">auto</code> <?php echo t("(default): Automatically matches the visitor's system theme preference. Auto is recommended for most implementations as it respects the visitor's preferences and provides the best accessibility experience."); ?></li>
            <li><code dir="auto">light</code>: <?php echo t('Light theme with bright colors and clear contrast. Light theme works best on bright backgrounds and provides high contrast for readability.'); ?></li>
            <li><code dir="auto">dark</code>: <?php echo t('Dark theme optimized for dark interfaces. Dark theme is ideal for dark interfaces, gaming sites, or applications with dark color schemes.'); ?></li>
        </ul>
    </div>
</div>

<div class="form-group">
    <?php
    echo $form->label('size', t('Size'));
echo $form->select('size', [
    '' => t('Select a size'),
    'normal' => t('Normal'),
    'flexible' => t('Flexible'),
    'compact' => t('Compact'),
], $config->get('captcha.cfTurnstile.size') ?? 'normal');
?>
    <div class="help-block">
        <p><?php echo t('Widget size'); ?></p>
        <ul>
            <li><code>normal</code> <?php echo t(': The default size works well for most desktop and mobile layouts. Use this if you have adequate horizontal space on your website or form.'); ?></li>
            <li><code>flexible</code> <?php echo t(': Automatically adapts to the container width while maintaining minimum usability. Use this for responsive designs that need to work across all screen sizes.'); ?></li>
            <li><code>compact</code> <?php echo t(': Ideal for mobile interfaces, sidebars, or any space where horizontal space is limited. The compact widget is taller than normal to accommodate the smaller width.'); ?></li>
        </ul>
    </div>
</div>

<div class="form-group">
    <?php
    echo $form->label('execution', t('Execution'));
echo $form->select('execution', [
    '' => t('Select an execution'),
    'render' => t('Render'),
    'execute' => t('Execute'),
], $config->get('captcha.cfTurnstile.execution') ?? 'render');
?>
    <div class="help-block">
        <p><?php echo t('Control when the challenge runs and a token is generated.'); ?></p>
        <ul>
            <li>
                <code>render</code> <?php echo t('(default): The challenge runs automatically after calling the render() function and provides immediate protection as soon as the widget loads. The challenge runs in the background while the page loads, ensuring the token is ready when the visitor submits data.'); ?>
            </li>
            <li>
                <code>execute</code><?php echo t(': The challenge runs after calling the turnstile.execute() function separately and gives you precise control over when verification occurs. This option is useful for multi-step forms, conditional verification, or when you want to defer the challenge until the visitor actually attempts to submit data. This can improve page load performance and visitor experience by only running verification when needed.'); ?><br />
                <strong><?php echo t('Common scenarios'); ?></strong>
                <ul>
                    <li><?php echo t('Multi-step forms: Run verification only on the final step.'); ?></li>
                    <li><?php echo t('Conditional protection: Only verify visitors who meet certain criteria.'); ?></li>
                    <li><?php echo t('Performance optimization: Defer verification to reduce initial page load time.'); ?></li>
                    <li><?php echo t('User-triggered verification: Let visitors manually start the verification process.'); ?></li>
                </ul>
            </li>
        </ul>
    </div>
</div>

<div class="form-group">
    <?php
    echo $form->label('appearance', t('Appearance'));
echo $form->select('appearance', [
    '' => t('Select an appearance'),
    'always' => t('Always'),
    'execute' => t('Execute'),
    'interaction-only' => t('Interaction Only'),
], $config->get('captcha.cfTurnstile.appearance') ?? 'always');
?>
    <div class="help-block">
        <p><?php echo t('Control when the widget becomes visible to visitors using the appearance mode.'); ?></p>
        <ul>
            <li>
                <code>always</code> <?php echo t('(default): The widget is always visible from page load. This is the best option for most implementations where you want your visitors to see the widget immediately as it provides clear visual feedback that security verification is in place.'); ?>
            </li>
            <li>
                <code>execute</code><?php echo t(': The widget only becomes visible after the challenge begins. This is useful for when you need to control the timing of widget appearance, such as showing it only when a visitor starts filling out a form or selecting a submit button.'); ?>
            </li>
            <li>
                <code>interaction-only</code><?php echo t(': The widget becomes visible only when visitor interaction is required and provides the cleanest visitor experience. Most visitors will never see the widget, but suspected bots will encounter the interactive challenge.'); ?>
            </li>
        </ul>

    </div>
</div>
