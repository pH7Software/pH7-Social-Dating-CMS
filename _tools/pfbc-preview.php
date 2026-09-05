<?php
/**
 * Local PFBC preview and browser regression checks. No database or application login required.
 * Run: php -S 127.0.0.1:8642 -t .
 * Open: http://127.0.0.1:8642/_tools/pfbc-preview.php
 *
 * @author Pierre-Henry Soria <hello@ph7builder.com>
 * @license MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

if (PHP_SAPI !== 'cli-server' || !in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)) {
    http_response_code(404);
    exit;
}

if (isset($_GET['reply'])) {
    header('Content-Type: application/json');
    if ($_GET['reply'] === 'failure') {
        http_response_code(503);
        echo '{}';
    } else {
        echo json_encode(['errors' => ['Please check your email address.']]);
    }
    exit;
}

session_name('ph7_pfbc_preview');
session_start();
define('PH7', true);
define('PH7_DS', '/');
define('PH7_SH', '/');
define('PH7_JS', 'js/');
define('PH7_URL_STATIC', '/static/');
define('PH7_PATH_PROTECTED', dirname(__DIR__) . '/_protected/');
define('PH7_PATH_FRAMEWORK', PH7_PATH_PROTECTED . 'framework/');
require PH7_PATH_FRAMEWORK . 'Loader/Autoloader.php';
PH7\Framework\Loader\Autoloader::getInstance()->init();
require PH7_PATH_FRAMEWORK . 'Layout/Form/Engine/PFBC/Form.class.php';

// Fixed English labels keep this isolated fixture independent of application settings.
function t(string $sText): string
{
    return $sText;
}

function nt(string $sOne, string $sMany, int $iCount): string
{
    return $iCount === 1 ? $sOne : $sMany;
}

$sTheme = ($_GET['theme'] ?? '') === 'premium' ? 'premium' : 'base';
$sScheme = ($_GET['scheme'] ?? '') === 'dark' ? 'dark' : 'light';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PFBC form preview</title>
    <link rel="stylesheet" href="/static/css/js/jquery/smoothness/jquery-ui.css">
    <link rel="stylesheet" href="/static/css/bootstrap.css">
    <link rel="stylesheet" href="/static/css/bootstrap_customize.css">
    <?php foreach (['common', 'style', 'layout', 'color', 'form'] as $sCss): ?>
    <link rel="stylesheet" href="/templates/themes/<?= $sTheme ?>/css/<?= $sCss ?>.css">
    <?php endforeach; ?>
    <style>
    <?php
    // Exercise the actual theme's dark rules without changing the developer's OS preferences.
    echo str_replace('(prefers-color-scheme: dark)', $sScheme === 'dark' ? 'all' : 'not all', file_get_contents(dirname(__DIR__) . '/templates/themes/base/css/design_system.css'));
    ?>
    :root { color-scheme: <?= $sScheme ?>; }
    </style>
    <script src="/static/js/jquery/jquery.js"></script>
</head>
<body>
<main class="container" style="padding: 24px 15px; max-width: 760px">
    <h1>PFBC form preview</h1>
    <p>Actual PFBC output with the bundled Bootstrap, jQuery and jQuery UI. Submissions stay in this local fixture.</p>
    <p><a href="?theme=base">Base theme</a> · <a href="?theme=premium">Premium theme</a> · <a href="theme-preview.html">Theme style guide</a></p>
    <p><a href="?theme=<?= $sTheme ?>&amp;scheme=light">Light appearance</a> · <a href="?theme=<?= $sTheme ?>&amp;scheme=dark">Dark appearance</a></p>
    <h2>Login</h2>
    <?php
    $oLogin = new PFBC\Form('preview_login');
    $oLogin->configure(['view' => new PFBC\View\Horizontal(), 'onsubmit' => 'return false']);
    $oLogin->addElement(new PFBC\Element\Email('', 'email', ['placeholder' => 'Your email', 'aria-label' => 'Login email', 'style' => 'width:190px'], false));
    $oLogin->addElement(new PFBC\Element\Password('', 'password', ['placeholder' => 'Your password', 'aria-label' => 'Login password', 'style' => 'width:190px']));
    $oLogin->addElement(new PFBC\Element\Button('Login', 'submit', ['icon' => 'key']));
    $oLogin->render();

    $oForm = new PFBC\Form('preview_join');
    $oForm->configure(['onsubmit' => 'return false']);
    $oForm->addElement(new PFBC\Element\Textbox('Your first name', 'first_name', ['required' => 1, 'placeholder' => 'First name']));
    $oForm->addElement(new PFBC\Element\Email('Your email', 'email', ['required' => 1, 'placeholder' => 'you@example.com'], false));
    $oForm->addElement(new PFBC\Element\Password('Your password', 'password', ['description' => 'Use a unique password for this account.']));
    $oForm->addElement(new PFBC\Element\Range('Age', 'age', ['value' => 30, 'min' => 18, 'max' => 99]));
    $oForm->addElement(new PFBC\Element\Range('Distance', 'distance', ['value' => 10, 'min' => 1, 'max' => 100]));
    $oForm->addElement(new PFBC\Element\Select('Country', 'country', ['AU' => 'Australia', 'BE' => 'Belgium']));
    $oForm->addElement(new PFBC\Element\Select('Interests', 'interests', ['music' => 'Music', 'travel' => 'Travel', 'food' => 'Food'], ['multiple' => 1]));
    $oForm->addElement(new PFBC\Element\Radio('Visibility', 'visibility', ['public' => 'Public profile', 'private' => 'Members only'], ['inline' => 1]));
    $oForm->addElement(new PFBC\Element\Textarea('About you', 'description', ['maxlength' => 100]));
    $oForm->addElement(new PFBC\Element\File('Profile photo', 'photo', ['accept' => 'image/*']));
    $oForm->addElement(new PFBC\Element\Checkbox('', 'agree', [1 => 'I agree to the community guidelines.'], ['value' => 1]));
    $oForm->addElement(new PFBC\Element\Button('Join now', 'submit', ['icon' => 'person']));
    PFBC\Form::clearErrors('preview_join');
    PFBC\Form::setError('preview_join', 'Please check your email address and try again.');
    $oForm->render();
    ?>
    <h2>Ajax recovery</h2>
    <?php
    foreach (['validation', 'failure', 'plain'] as $sReply) {
        $oAjax = new PFBC\Form('preview_' . $sReply);
        $oAjax->configure([
            'ajax' => true,
            'action' => '?reply=' . ($sReply === 'plain' ? 'failure' : $sReply),
            'prevent' => $sReply === 'plain' ? ['jQueryUIButtons'] : []
        ]);
        $oAjax->addElement(new PFBC\Element\Button('Test ' . $sReply, 'submit', $sReply === 'plain' ? ['class' => 'btn btn-danger'] : []));
        $oAjax->render();
    }
    ?>
    <h2>Browser regression checks</h2>
    <button type="button" class="btn btn-default" id="run_checks">Run interaction checks</button>
    <output id="preview_result" aria-live="polite"></output>
</main>
<script src="/static/js/bootstrap.js"></script>
<script src="/static/js/str.js"></script>
<script src="/static/js/password_toggle.js"></script>
<script src="/static/js/jquery/jquery-ui.js"></script>
<script src="/static/js/form.js"></script>
<script src="pfbc-preview.js"></script>
</body>
</html>
