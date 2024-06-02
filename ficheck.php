<?php

require __DIR__ . '/vendor/autoload.php';
$app        = require_once __DIR__ . '/bootstrap/app.php';
$stack      = GuzzleHttp\HandlerStack::create();
$httpClient = new GuzzleHttp\Client([
    'handler' => $stack,
]);
function url()
{
    if (isset($_SERVER['HTTPS']))
    {
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
        return $protocol . "://" . $_SERVER['HTTP_HOST'];
    }
    else
    {
        $protocol = 'http';
        return $protocol . "://" . $_SERVER['HTTP_HOST'] . str_replace('/ficheck.php', '', $_SERVER['PHP_SELF']);
    }

}

$url = url();

try
{
    $apiResponse = $httpClient->get($url . '/api/v1/fi-check/addons');
    if ($apiResponse->getStatusCode() == 200)
    {
        $addons = json_decode($apiResponse->getBody());
    }
    else
    {
        $addons = [];
    }
}
catch (\Exception $e)
{
    $addons = [];
}

try
{
    $apiResponse = $httpClient->get($url . '/api/v1/fi-check/version');
    if ($apiResponse->getStatusCode() == 200)
    {
        $version = json_decode($apiResponse->getBody());
    }
    else
    {
        $version = [];
    }
}
catch (\Exception $e)
{
    $version = [];
}

try
{
    $apiResponse = $httpClient->get($url . '/api/v1/fi-check/migration');

    if ($apiResponse->getStatusCode() == 200)
    {
        $migration = json_decode($apiResponse->getBody());
    }
    else
    {
        $migration = [];
    }
}
catch (\Exception $e)
{
    $migration = [];
}

ob_start();
phpinfo(INFO_MODULES);
$info = ob_get_contents();
ob_end_clean();
$info = stristr($info, 'Client API version');
preg_match('/[1-9].[0-9].[1-9][0-9]/', $info, $match);
$mySqlVersion = $match[0];

$requirements1 = [
    [
        'requirement' => 'PHP Version',
        'required'    => '8.1.0',
        'actual'      => PHP_VERSION,
        'result'      => ((version_compare(PHP_VERSION, '8.1.0') >= 0) ? 1 : 0),
    ],
    [
        'requirement' => 'Fileinfo Extension',
        'required'    => 'Yes',
        'actual'      => ((extension_loaded('fileinfo')) ? 'Yes' : 'No'),
        'result'      => ((extension_loaded('fileinfo')) ? 1 : 0),
    ],
    [
        'requirement' => 'OpenSSL Extension',
        'required'    => 'Yes',
        'actual'      => ((extension_loaded('openssl')) ? 'Yes' : 'No'),
        'result'      => ((extension_loaded('openssl')) ? 1 : 0),
    ],
    [
        'requirement' => 'PDO Extension',
        'required'    => 'Yes',
        'actual'      => ((extension_loaded('pdo')) ? 'Yes' : 'No'),
        'result'      => ((extension_loaded('pdo')) ? 1 : 0),
    ],
    [
        'requirement' => 'PDO MySQL Extension',
        'required'    => 'Yes',
        'actual'      => ((extension_loaded('pdo_mysql')) ? 'Yes' : 'No'),
        'result'      => ((extension_loaded('pdo_mysql')) ? 1 : 0),
    ],
    [
        'requirement' => 'MBString Extension',
        'required'    => 'Yes',
        'actual'      => ((extension_loaded('mbstring')) ? 'Yes' : 'No'),
        'result'      => ((extension_loaded('mbstring')) ? 1 : 0),
    ],
    [
        'requirement' => 'Tokenizer Extension',
        'required'    => 'Yes',
        'actual'      => ((extension_loaded('tokenizer')) ? 'Yes' : 'No'),
        'result'      => ((extension_loaded('tokenizer')) ? 1 : 0),
    ],
    [
        'requirement' => 'Graphics Drawing Extension',
        'required'    => 'Yes',
        'actual'      => ((extension_loaded('gd')) ? 'Yes' : 'No'),
        'result'      => ((extension_loaded('gd')) ? 1 : 0),
    ],
    [
        'requirement' => 'XML PHP Extension',
        'required'    => 'Yes',
        'actual'      => ((extension_loaded('xml')) ? 'Yes' : 'No'),
        'result'      => ((extension_loaded('xml')) ? 1 : 0),
    ],
    [
        'requirement' => 'DOM PHP Extension',
        'required'    => 'Yes',
        'actual'      => ((extension_loaded('dom')) ? 'Yes' : 'No'),
        'result'      => ((extension_loaded('dom')) ? 1 : 0),
    ],
    [
        'requirement' => 'Iconv PHP Extension',
        'required'    => 'Yes',
        'actual'      => ((extension_loaded('iconv')) ? 'Yes' : 'No'),
        'result'      => ((extension_loaded('iconv')) ? 1 : 0),
    ],
];

// Mod Rewrite only supported by Apache-based webservers.
if (function_exists('apache_get_modules'))
{
    $modRewriteRequirement = [
        [
            'requirement' => 'Mod Rewrite Extension for SEO friendly URL',
            'required'    => 'Yes',
            'actual'      => ((in_array('mod_rewrite', apache_get_modules())) ? 'Yes' : 'No'),
            'result'      => ((in_array('mod_rewrite', apache_get_modules())) ? 1 : 0),
        ],
    ];
}
else
{
    $modRewriteRequirement = [
        [
            'requirement' => 'Mod Rewrite Extension for SEO friendly URL',
            'required'    => 'No',
            'actual'      => 'Unable to access method apache_get_modules()',
            'result'      => '0',
        ],
    ];
}

$requirements2 = [
    [
        'requirement' => 'Webserver',
        'required'    => 'Yes',
        'actual'      => $_SERVER["SERVER_SOFTWARE"],
        'result'      => 1,
    ],
    [
        'requirement' => 'Database Version',
        'required'    => 'Yes',
        'actual'      => 'MySql : ' . $mySqlVersion,
        'result'      => 1,
    ],
];

$requirements3 = [
    [
        'requirement' => 'Version Number',
        'required'    => '-',
        'actual'      => isset($version->data->version) ? $version->data->version : '',
        'result'      => 1,
    ],
    [
        'requirement' => 'Key',
        'required'    => '-',
        'actual'      => isset($version->data->key) ? substr_replace($version->data->key, str_repeat('*', 24), 4, 24) : '',
        'result'      => 1,
    ],
    [
        'requirement' => 'Last Migration',
        'required'    => '-',
        'actual'      => isset($migration->data->migration) ? $migration->data->migration : '',
        'result'      => 1,
    ],
];


$requirements = array_merge($requirements1, $modRewriteRequirement, $requirements2, $requirements3);

function logReader($filename, $lines = 600, $buffer = 4096)
{
    // Open the file
    $f = fopen($filename, "rb");

    // Jump to last character
    fseek($f, -1, SEEK_END);

    // Read it and adjust line number if necessary
    // (Otherwise the result would be wrong if file doesn't end with a blank line)
    if (fread($f, 1) != "\n") $lines -= 1;

    // Start reading
    $output = '';
    $chunk  = '';

    // While we would like more
    while (ftell($f) > 0 && $lines >= 0)
    {
        // Figure out how far back we should jump
        $seek = min(ftell($f), $buffer);

        // Do the jump (backwards, relative to where we are)
        fseek($f, -$seek, SEEK_CUR);

        // Read a chunk and prepend it to our output
        $output = ($chunk = fread($f, $seek)) . $output;

        // Jump back to where we started reading
        fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);

        // Decrease our line counter
        $lines -= substr_count($chunk, "\n");
    }

    // While we have too many lines
    // (Because of buffer size we might have read too many)
    while ($lines++ < 0)
    {
        // Find first newline and remove all text before that
        $output = substr($output, strpos($output, "\n") + 1);
    }

    // Close file and return
    fclose($f);
    return $output;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>FusionInvoice Check</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, follow">

    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
    <link rel="manifest" href="site.webmanifest">
    <link rel="mask-icon" href="safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="assets/dist/css/fonts.google.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet"
          href="assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
</head>
<body>
<div class="wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="text-center">FI Config Check</h1>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h6>Welcome to FusionInvoice <?php echo isset($version->data->version) ? $version->data->version : '--'; ?> System Check.</h6>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-sm table-striped">
                                <tr>
                                    <th>Requirement</th>
                                    <th>Required</th>
                                    <th>Actual</th>
                                    <th>Result</th>
                                </tr>
                                <?php foreach ($requirements as $requirement)
                                { ?>
                                    <tr>
                                        <td><?php echo $requirement['requirement']; ?></td>
                                        <td><?php echo $requirement['required']; ?></td>
                                        <td><?php echo $requirement['actual']; ?></td>
                                        <td><?php if ($requirement['result'] == 1)
                                            { ?><span style="color: green;">Pass</span><?php }
                                            else
                                            { ?><span style="color: red;">Fail</span><?php } ?></td>
                                    </tr>
                                <?php } ?>
                                <?php
                                if (isset($addons->data) && count($addons->data) > 0)
                                {
                                    foreach ($addons->data as $key => $addon)
                                    {
                                        ?>
                                        <tr>
                                            <td><strong><?php echo $key == 0 ? 'Add-ons' : ''; ?></strong></td>
                                            <td>-</td>
                                            <td><?php echo $addon->name; ?></td>
                                            <td><span style="color: green;">Pass</span></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                            </table>
                            <br>
                            <table class="table table-sm table-striped">
                                <tr>
                                    <td align="left"><strong>Recent Log:</strong></td>
                                </tr>
                                <tr>
                                    <td>
                                        <textarea style="width: 100%; min-height: 300px;" readonly="readonly" disabled>
                                            <?php echo logReader('storage/logs/laravel.log') ?>
                                        </textarea>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
</body>
</html>
