<!doctype html>
<html lang="<?=_LANG ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf8">
 	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta charset="UTF-8">
	<meta name="Author" content="<?=$lang['author'] ?>">
	<meta name="robots" content="NOINDEX,NOFOLLOW,noarchive" />
	<title><?=$lang['timesitetitle'] ?></title>
	<link href="<?=$lang['cssFilePath'];?>" rel="stylesheet" type="text/css" />
</head>

<body>
	<div id="holder">
		<div id="content">
			<h1><?=$lang['sitetitle'] ?></h1>
				<h2 id="main"><?= $lang["h2"];?></h2>
				<h3><?= $lang["h3"];?></h3>
					<p><?= $lang["p"];?></p>
	
				<div style="text-align:center;margin-top:50px;"><a href="<?= $lang['url'];?>"><?= $lang["link"];?></a></div>
			
			<h5></h5>
			<span id="bottom"><a href="<?=$lang['root'];?>/" title="<?=$lang['sitetitle'] ?>"><?=$lang['sitetitle'] ?></a></span>
		</div>

	</div>
</body>
</html>