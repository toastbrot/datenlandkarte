<?php
    if (!$_POST) header('Location: input.php');

    require_once('lib.php');

    $keys = get_keys_by_vis($_POST);

    $error = array();
    if (!$keys)
    {
        $error = array('Es konnte keine valide Karte verwendet werden');
    } else {
        $data = get_data($_POST, $keys);

        if (empty($_POST['fac']))
            $fac = 1.0;
        else
            $fac = (float)$_POST['fac'];

        $error = _error_msg_for_data($data);
        if ($error === false) // no error
            $data = include_factor($data, $fac);
        else
            $error = array($error);
    }

    // Create files
    if (empty($error))
    {
        $image = select_svg_file($_POST);

        // sanitize parameters
        $file_title = $_POST['title'];
        $file_title = preg_replace('/[[:^alnum:]]/', '_', $file_title);
        $file_subtitle = $_POST['subtitle'];
        $file_subtitle = preg_replace('/[[:^alnum:]]/', '_', $file_subtitle);

        $title = htmlspecialchars($_POST['title'], ENT_NOQUOTES);
        $subtitle = htmlspecialchars($_POST['subtitle'], ENT_NOQUOTES);

        if ($file_title)
            $file_title = '-'.$file_title;
        if ($file_subtitle)
            $file_subtitle = '-'.$file_subtitle;

        $dec = $_POST['dec'];
        if (strlen($dec) == 0)
            $dec = 2;
        else
            $dec = (int)$_POST['dec'];
        if ($dec > 3)
            $dec = 3;

        $colors = $_POST['colors'];
        if (2 < (int)$colors && (int)$colors < 10)
            $colors = (int)$colors;
        else
            $colors = 10;

        if (file_exists($image))
        {
            // Create SVG
            $svg = substitute($image, $title, $subtitle,
                $dec, $colors, (int)$_POST['grad'], $data);

            $date = date('Ymd');
            $img_path = $location_creation.
                $date.$file_title.$file_subtitle;

            // Delete old files
            @unlink($img_path.'.svg');
            @unlink($img_path.'.png');
            @unlink($img_path.'_big.png');

            $fp = fopen($img_path.'.svg', 'w');
            if ($fp)
            {
                $a = fwrite($fp, $svg);
                if (!$a) {
                    $error[] = 'Konnte Datei nicht schreiben. '.
                            'Keine Zugriffsrechte.';
                } else {
                    // PNG1 aus SVG erzeugen
                    exec('convert '.$img_path.'.svg '.$img_path.'.png');
                    // PNG2 aus SVG erzeugen
                    exec('convert -scale 300% '.$img_path.'.svg '.$img_path.'_big.png');
                }
                fclose($fp);
            } else {
                $error[] = 'Konnte Datei nicht öffnen. '.
                        'Keine Zugriffsrechte.';
            }
        } else {
            $error[] = 'Konnte Basiskarte ('.
                htmlspecialchars(basename($image), ENT_NOQUOTES).
                ') nicht finden.';
        }
    }
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="de-DE"
 xmlns:og='http://opengraphprotocol.org/schema/'>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>Datenlandkarte Format wählen</title>
<meta name="description" content="Hier können Sie selbst Datenlandkarten erstellen. Gleichzeitig werden, falls Sie diese Option aktiviert lassen, die Rohdaten der Visualisierung gespeichert und" />
<meta name="keywords" content="Bezirke, Kärnten, Alle, Ebenen, Daten, Bundesländer" />
<meta name="robots" content="index, follow" />
<link rel="canonical" href="http://www.datenlandkarten.at/datenlandkarte-erstellen/" />

<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />

<link rel="stylesheet" href="http://www.datenlandkarten.at/wp-content/themes/datenlandkarten/style.css" type="text/css" media="screen" />
<!--[if IE 6]><link rel="stylesheet" href="http://www.datenlandkarten.at/wp-content/themes/datenlandkarten/style.ie6.css" type="text/css" media="screen" /><![endif]-->
<!--[if IE 7]><link rel="stylesheet" href="http://www.datenlandkarten.at/wp-content/themes/datenlandkarten/style.ie7.css" type="text/css" media="screen" /><![endif]-->
<link rel="pingback" href="http://www.datenlandkarten.at/xmlrpc.php" />
<link rel="alternate" type="application/rss+xml" title="Datenlandkarten.at &raquo; Feed" href="http://www.datenlandkarten.at/feed/" />
<link rel="alternate" type="application/rss+xml" title="Datenlandkarten.at &raquo; Kommentar Feed" href="http://www.datenlandkarten.at/comments/feed/" />
<link rel='stylesheet' id='NextGEN-css'  href='http://www.datenlandkarten.at/wp-content/plugins/nextgen-gallery/css/nggallery.css?ver=1.0.0' type='text/css' media='screen' />
<link rel='stylesheet' id='shutter-css'  href='http://www.datenlandkarten.at/wp-content/plugins/nextgen-gallery/shutter/shutter-reloaded.css?ver=1.3.0' type='text/css' media='screen' />
<link rel='stylesheet' id='feedreading_style-css'  href='http://www.datenlandkarten.at/wp-content/plugins/feed-reading-blogroll/css/feedreading_blogroll.css?ver=1.5.6' type='text/css' media='all' />
<link rel='stylesheet' id='prlipro-post-css'  href='http://www.datenlandkarten.at/wp-content/plugins/pretty-link/pro/css/prlipro-post.css?ver=3.0.4' type='text/css' media='all' />
<script type='text/javascript' src='http://www.datenlandkarten.at/wp-content/plugins/nextgen-gallery/shutter/shutter-reloaded.js?ver=1.3.0'></script>
<script type='text/javascript' src='http://www.datenlandkarten.at/wp-includes/js/swfobject.js?ver=2.2'></script>
<script type='text/javascript' src='http://www.datenlandkarten.at/wp-includes/js/jquery/jquery.js?ver=1.4.2'></script>
<script type='text/javascript' src='http://www.datenlandkarten.at/wp-includes/js/comment-reply.js?ver=20090102'></script>
<script type='text/javascript' src='http://www.datenlandkarten.at/wp-content/feedreading_blogroll.js?ver=1.5.6'></script>
<link rel="EditURI" type="application/rsd+xml" title="RSD" href="http://www.datenlandkarten.at/xmlrpc.php?rsd" />
<link rel="wlwmanifest" type="application/wlwmanifest+xml" href="http://www.datenlandkarten.at/wp-includes/wlwmanifest.xml" /> 
<link rel='index' title='Datenlandkarten.at' href='http://www.datenlandkarten.at/' />
<link rel='next' title='Galerie' href='http://www.datenlandkarten.at/galerie/' />
				<meta name="DC.publisher" content="Datenlandkarten.at" />
		<meta name="DC.publisher.url" content="http://www.datenlandkarten.at/" />
		<meta name="DC.title" content="Datenlandkarte erstellen" />
		<meta name="DC.identifier" content="http://www.datenlandkarten.at/datenlandkarte-erstellen/" />
		<meta name="DC.date.created" scheme="WTN8601" content="2011-02-02T20:09:28" />
		<meta name="DC.created" scheme="WTN8601" content="2011-02-02T20:09:28" />
		<meta name="DC.date" scheme="WTN8601" content="2011-02-02T20:09:28" />
		<meta name="DC.creator.name" content="Harm, Robert" />
		<meta name="DC.creator" content="Harm, Robert" />
		<meta name="DC.rights.rightsHolder" content="@RobertHarm" />		
		<meta name="DC.language" content="de-DE" scheme="rfc1766" />
		<meta name="DC.rights.license" content="http://creativecommons.org/licenses/by/3.0/at/" />
		<meta name="DC.license" content="http://creativecommons.org/licenses/by/3.0/at/" />
	<!--Facebook Like Button OpenGraph Settings Start-->
	<meta property="og:site_name" content="Datenlandkarten.at"/>
	<meta property="og:title" content="Datenlandkarte erstellen"/>
		<meta property="og:description" content="Hier können Sie selbst Datenlandkarten erstellen. Gleichzeitig werden, falls Sie diese Option aktiviert lassen, die Rohdaten der Visualisie"/>
	
	<meta property="og:url" content="http://www.datenlandkarten.at/datenlandkarte-erstellen/"/>
	<meta property="fb:admins" content="1039929046" />
	<meta property="fb:app_id" content="192140977480316" />
	<meta property="og:image" content="http://www.datenlandkarten.at/wp-content/uploads/opengraph.png" />
	<meta property="og:type" content="article" />
		<!--Facebook Like Button OpenGraph Settings End-->
	      <link rel="shorturl" href="http://datenlandkarte.at/gs8" />
    
<meta name='NextGEN' content='1.7.3' />
<script type="text/javascript" src="http://www.datenlandkarten.at/wp-content/themes/datenlandkarten/script.js"></script>
<style type="text/css">
<!--
    table {
        width: 100%;
    }
    td {
        vertical-align: top;
    }
    select {
        min-width: 50%;
    }
    textarea {
        width: 100%;
    }
    input, textarea { background-color: #CCC; }
    input:hover, textarea:hover { background-color: #EEE; }

    .error {
        color: #F00;
    }
    .subselect {
        margin-left: 30px;
    }
    .download {
        background-color: #EEE;
        margin: 20px;
        clear: both;
        padding: 10px;
    }
-->
</style>
</head>
<body class="page page-id-2 page-template page-template-default">
<div id="art-main">
    <div class="art-sheet">
        <div class="art-sheet-tl"></div>
        <div class="art-sheet-tr"></div>
        <div class="art-sheet-bl"></div>
        <div class="art-sheet-br"></div>
        <div class="art-sheet-tc"></div>
        <div class="art-sheet-bc"></div>
        <div class="art-sheet-cl"></div>
        <div class="art-sheet-cr"></div>
        <div class="art-sheet-cc"></div>
        <div class="art-sheet-body">
            <div class="art-header">
                <div class="art-header-center">
                    <div class="art-header-png"></div>
                    <div class="art-header-jpeg"></div>
                </div>
                <div class="art-headerobject"></div>
                <div class="art-logo">
                                <h1 id="name-text" class="art-logo-name"><a href="http://www.datenlandkarten.at/">Datenlandkarten.at</a></h1>
                                                    <h2 id="slogan-text" class="art-logo-text">Erstelle deine eigene Visualisierung von Gemeinde-, Bezirks-, und Bundesland-Daten</h2>
                                </div>
            </div>
            <div class="art-nav">
            	<div class="art-nav-l"></div>
            	<div class="art-nav-r"></div>
            	
<ul class="art-menu">
	<li><a href="http://www.datenlandkarten.at" title="Startseite"><span class="l"> </span><span class="r"> </span><span class="t">Startseite</span></a>
	</li>
	<li class="art-menu-li-separator"><span class="art-menu-separator"> </span></li>
	<li class="active"><a class="active" href="http://www.datenlandkarten.at/datenlandkarte-erstellen/" title="Datenlandkarte erstellen"><span class="l"> </span><span class="r"> </span><span class="t">Datenlandkarte erstellen</span></a>
	</li>
	<li class="art-menu-li-separator"><span class="art-menu-separator"> </span></li>
	<li><a href="http://www.datenlandkarten.at/rohdaten/" title="Rohdaten"><span class="l"> </span><span class="r"> </span><span class="t">Rohdaten</span></a>
	</li>
	<li class="art-menu-li-separator"><span class="art-menu-separator"> </span></li>
	<li><a href="http://www.datenlandkarten.at/galerie/" title="Galerie"><span class="l"> </span><span class="r"> </span><span class="t">Galerie</span></a>
	</li>
	<li class="art-menu-li-separator"><span class="art-menu-separator"> </span></li>
	<li><a href="http://www.datenlandkarten.at/blog/" title="Blog"><span class="l"> </span><span class="r"> </span><span class="t">Blog</span></a>
	</li>
	<li class="art-menu-li-separator"><span class="art-menu-separator"> </span></li>
	<li><a href="http://www.datenlandkarten.at/impressum/" title="Impressum"><span class="l"> </span><span class="r"> </span><span class="t">Impressum</span></a>
	</li>
</ul>
            </div>
<div class="art-content-layout">
    <div class="art-content-layout-row">
        <div class="art-layout-cell art-content">
			


<div class="art-post post-2 page type-page hentry" id="post-2">
	    <div class="art-post-body">
	            <div class="art-post-inner art-article">


        <h2 class="art-postheader">Datenlandkarte speichern</h2>
        <div class="art-postcontent">
          <noscript>
            <p>
              Dieses Formular arbeitet mit Javascript. Bitte aktivieren
              Sie Javascript in ihrem Browser, wenn möglich.
            </p>
          </noscript>
<?php
    if (!empty($error)) {
?>
          <div class="error">
            <p>Es traten mindestens 1 Fehler auf:<p>
            <ul>
<?php
        foreach ($error as $e) {
?>

              <li><?=htmlspecialchars($e, ENT_NOQUOTES); ?></li>
<?php
        }
?>
            </ul>
          </div>
<?php
    } else {
?>

          <div class="download">
           <a href="<?=$img_path; ?>.svg">
             <img src="img/svg.png" alt="SVG Graphic Datenlandkarte" style="float:left">
           </a>
           <h5><a href="<?=$img_path; ?>.svg">Download SVG</a></h5>
           <p>Scalable Vector Graphics</p>
          </div>

          <div class="download">
           <a href="<?=$img_path; ?>.png">
             <img src="img/png.png" alt="PNG Graphic Datenlandkarte" style="float:left">
           </a>
           <h5><a href="<?=$img_path; ?>.png">Download PNG</a></h5>
           <p>Portable Network Graphics</p>
          </div>

          <div class="download">
           <a href="<?=$img_path; ?>_big.png">
             <img src="img/png.png" alt="PNG Graphic Datenlandkarte" style="float:left">
           </a>
           <h5><a href="<?=$img_path; ?>_big.png">Download PNG (3fache Größe)</a></h5>
           <p>Portable Network Graphics</p>
          </div>
<?php } ?>
                    <div class="cleared"></div>
            <div class="cleared"></div>
        </div>
    </div>


          <div class="cleared"></div>
        </div>
</div>
<div class="cleared"></div>
    <div class="art-footer">
                <div class="art-footer-t"></div>
                <div class="art-footer-l"></div>
                <div class="art-footer-b"></div>
                <div class="art-footer-r"></div>
                <div class="art-footer-body">


                  <div class="art-footer-text">
                      <p><div style="float:left;"><a href="http://www.open3.at" target="_blank" title="Webseite open3.at aufrufen"><img src="http://www.datenlandkarten.at/wp-content/uploads/open3logo.png" width="177" height="33"></a></div>
<div style="float:right;text-align:right;"><a href="http://www.opendefinition.org/okd/deutsch/" target="_blank" title="Definition "Offenes Wissen" auf http://opendefinition.org/ anzeigen"><img src="http://www.datenlandkarten.at/wp-content/uploads/badge-od.png" width="80" height="15"> <img src="http://www.datenlandkarten.at/wp-content/uploads/badge-ok.png" width="80" height="15"> <img src="http://www.datenlandkarten.at/wp-content/uploads/badge-oc.png" width="80" height="15"></a><br/>
<a href="/impressum" style="text-decoration:none;" title="Impressum anzeigen">Ein Projekt von open3, dem Netzwerk zur Förderung von openSociety, openGovernment und OpenData</a></p></div>                  </div>
                    <div class="cleared"></div>
                </div>
            </div>
            <div class="cleared"></div>
        </div>
    </div>
    <div class="cleared"></div>
    <p class="art-page-footer"></p>
</div>
    <div id="wp-footer">
    </div>
</body>
</html>
