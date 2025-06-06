<!doctype html>
<html lang="zh">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf8">
 	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta charset="UTF-8">
	<meta name="Author" content="<?=$lang['author'] ?>">
	<meta name="robots" content="NOINDEX,NOFOLLOW,noarchive" />
	<title><?=$lang['timesitetitle'] ?></title>
	
	<!-- Bootstrap 3.3.7 -->
	<link href="<?=$lang['cssPath'];?>bootstrap.min.css" rel="stylesheet">
	<link href="<?=$lang['cssPath'];?>bootstrap-theme.min.css" rel="stylesheet">
	
  	<link href="<?=$lang['cssPath'];?>footable.bootstrap.css" rel="stylesheet">
	
	<link href="<?=$lang['cssFilePath'];?>" rel="stylesheet" type="text/css" />
	<script src="<?=$lang['jqueryPath'];?>"></script>
	
	<script type="text/javascript">
		var footable;
	</script>
</head>

<body>
	<!-- Content -->
	<div class="container">
		<div>
			<div class="show-foo-div">
				<table class="table footatble table-bordered" id="list" data-sorting="true">
					<thead>
						<tr>
							<th data-name="id" data-filterable="false" data-sortable="false" data-visible="false">id</th>
							<th data-name="name" data-filterable="true" data-sortable="true">社团名</th>
							<th data-name="author" data-filterable="true" data-sortable="true">主催</th>
							<th data-name="email" data-filterable="true" data-sortable="true">邮箱</th>
							<th data-name="website" data-filterable="true" data-sortable="true">网站</th>
							<th data-name="mode" data-filterable="true" data-sortable="true">模式</th>
							<th data-name="space" data-filterable="true" data-sortable="true">摊位</th>
							<th data-name="nearby" data-filterable="true" data-sortable="true">接邻</th>
							<th data-name="work1" data-filterable="true" data-sortable="true">作品1</th>
							<th data-name="work2" data-filterable="true" data-sortable="true">作品2</th>
							<th data-name="work3" data-filterable="true" data-sortable="true">作品3</th>
							<th data-name="textarea" data-filterable="true" data-sortable="true">备注</th>
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>
	</div>
	<!-- Bootstrap 3.3.7 -->
	<script src="<?=$lang['jsPath'];?>bootstrap.min.js"></script>
	<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
	<script src="<?=$lang['jsPath'];?>ie10-viewport-bug-workaround.js"></script>
	
	<script src="<?=$lang['jsPath'];?>moment-with-locales.js"></script>
	<script src="<?=$lang['jsPath'];?>footable.js"></script>
	
	<script type="text/javascript">
	footable = FooTable.init('.table', {
		"sorting": {
			"enabled": true
		},
		"rows": <?=$lang['list'];?>,
		"filtering": {
			"enabled": true,
			"delay": 1200,
			"min": 1,
			"space": "OR",
			"placeholder": "社团名或作者或作品"
		},
		"empty": "暂无数据...",
	});
	</script>
</body>
</html>