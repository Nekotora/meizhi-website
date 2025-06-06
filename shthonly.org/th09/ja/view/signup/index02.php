<!doctype html>
<html lang="<?=_LANG ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf8">
 	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta charset="UTF-8">
	<meta name="Author" content="<?=$lang['author'] ?>">
	<title><?=$lang['timesitetitle'] ?></title>
	<link href="<?=$lang['cssFilePath'];?>" rel="stylesheet" type="text/css" />
	<script src="<?=$lang['jqueryPath'];?>"></script>
	<script src="<?=$lang['jsFilePath'];?>"></script>
    <script type="text/javascript">
		var verifyCallback = function(response) {
			$("#link").removeClass("gray");
			$('#link').removeAttr("disabled");
		};
		var onloadCallback = function() {
			grecaptcha.render('recaptcha', {
				'sitekey' : '6LcsV04UAAAAAL3XG98hPRAQf1d0QORM71HAZwwl',
				'callback' : verifyCallback,
				'theme' : 'light'
			});
		};
    </script>
</head>
<body>
	<div id="container">
		<div id="shape_box">
			<div id="shape"></div>
		</div>
		<form id="tableForm" name="tableForm" action="./?v=signup&a=post" method="post">
			<div id="table">
				<div id="main_info" class="info_div">
					<div id="left_div">
						<label class="normal_label" for="circle">サークル名（必要）：</label>
						<input type="text" id="circle" name="circle" class="input_long" maxlength="255" placeholder="サークル名を入力" required autofocus />
						
						<label class="normal_label" for="email">メールアドレス（必要）：</label>
						<input type="text" id="email" name="email" class="input_long" maxlength="255" placeholder="メールアドレスを入力" required />
					</div>
					<div id="right_div">
						
						<label class="normal_label" for="author">代表者ペンネーム（必要）：</label>
						<input type="text" id="author" name="author" class="input_long" maxlength="255" placeholder="ペンネームを入力" required />
						
						<label class="normal_label" for="website">webサイト（必要）：</label>
						<input type="text" id="website" name="website" class="input_long" maxlength="255" placeholder="webサイトを入力" required />
					</div>
				</div>
				<div id="space_info" class="info_div">
					<label class="normal_label" for="space_check">お申し込むスペース数：</label>
					<div id="space_check" class="sub_div">
						<label class="radio_label" for="one_space"><input id="one_space" type="radio" value="1" name="space" checked>１スペース</label>
	   					<label class="radio_label" for="two_space"><input id="two_space" type="radio" value="2" name="space">２スペース<span class="small">（三種類以上の作品を頒布するサークルのみお申し込むことが出来ます）</span></label>
					</div>
					
					<label class="normal_label" for="nearby">隣接配置希望サークル名：</label>
					<input type="text" id="nearby" name="nearby" class="input_long" maxlength="255" placeholder="サークル名を入力" />
				</div>
				<div id="work_info" class="info_div">
					<div class="line_div">
						<label class="line_first line_label">作品名：</label>
						<label class="line_second line_label">種類：</label>
						<label class="line_third line_label">人民元の定価：</label>
					</div>
					<div class="line_div">
						<input type="text" name="work1[name]" class="line_first" maxlength="255" placeholder="風の追跡" required />
						<input type="text" name="work1[type]" class="line_second" maxlength="255" placeholder="同人誌" required />
						<input type="text" name="work1[price]" class="line_third" maxlength="255" placeholder="３０元" required />
					</div>
					<div class="line_div">
						<input type="text" name="work2[name]" class="line_first" maxlength="255" placeholder="" />
						<input type="text" name="work2[type]" class="line_second" maxlength="255" placeholder="" />
						<input type="text" name="work2[price]" class="line_third" maxlength="255" placeholder="" />
					</div>
					<div class="line_div">
						<input type="text" name="work3[name]" class="line_first" maxlength="255" placeholder="" />
						<input type="text" name="work3[type]" class="line_second" maxlength="255" placeholder="" />
						<input type="text" name="work3[price]" class="line_third" maxlength="255" placeholder="" />
					</div>
					（※1,000円あたり人民元６０－７０元）
				</div>
				<div id="other_info" class="info_div">
					<label class="normal_label" for="space_check">お問い合わせ：</label>
					<textarea id="textarea" name="textarea" placeholder=""></textarea>
				</div>
				<div id="reg_info" class="info_div">
					<label class="normal_label" for="recaptcha">画像認証（Google reCAPTCHA）：</label>
					<div id="recaptcha"></div>
				</div>
			</div>
			<button id="link" class="info gray" type="submit" formtarget="_self" value="" disabled></button>
		</form>
	</div>
	<script src='https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit'></script>
    <script type="text/javascript">
		$('#link').attr("disabled", "disabled");
    </script>
</body>
</html>
