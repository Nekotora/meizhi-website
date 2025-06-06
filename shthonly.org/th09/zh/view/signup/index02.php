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
	<script src="<?=$lang['jsPath'];?>gt.js"></script>
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
						<label class="normal_label" for="circle">社团名（必填）：</label>
						<input type="text" id="circle" name="circle" class="input_long" maxlength="255" placeholder="请输入社团名" required autofocus />
						
						<label class="normal_label" for="email">电子邮箱（必填）：</label>
						<input type="text" id="email" name="email" class="input_long" maxlength="255" placeholder="请输入邮箱地址" required />
					</div>
					<div id="right_div">
						
						<label class="normal_label" for="author">社团代表/作者名（必填）：</label>
						<input type="text" id="author" name="author" class="input_long" maxlength="255" placeholder="请输入名称或ID" required />
						
						<label class="normal_label" for="website">社团/作者主页（必填）：</label>
						<input type="text" id="website" name="website" class="input_long" maxlength="255" placeholder="请输入网址" required />
					</div>
				</div>
				<div id="mode_info" class="info_div">
					<label class="normal_label" for="space_check">参加方式：</label>
					<div id="space_check" class="sub_div">
						<label class="radio_label" for="direct"><input id="direct" type="radio" value="1" name="mode" checked>直参</label>
	   					<label class="radio_label" for="lease"><input id="lease" type="radio" value="2" name="mode">寄售</label>
					</div>
				</div>
				<div id="space_info" class="info_div">
					<label class="normal_label" for="space_check">申请的摊位规格（寄售无需选择）：</label>
					<div id="space_check" class="sub_div">
						<label class="radio_label" for="one_space"><input id="one_space" type="radio" value="1" name="space" checked>半桌</label>
	   					<label class="radio_label" for="two_space"><input id="two_space" type="radio" value="2" name="space">全桌</label>
					</div>
					
					<label class="normal_label" for="nearby">希望与哪个社团相邻（寄售无需填写）：</label>
					<input type="text" id="nearby" name="nearby" class="input_long" maxlength="255" placeholder="请输入社团名" />
				</div>
				<div id="work_info" class="info_div">
					<div class="line_div">
						<label class="line_first line_label">作品名：</label>
						<label class="line_second line_label">类型：</label>
						<label class="line_third line_label">定价：</label>
					</div>
					<div class="line_div">
						<input type="text" name="work1[name]" class="line_first" maxlength="255" placeholder="风之追迹" required />
						<input type="text" name="work1[type]" class="line_second" maxlength="255" placeholder="同人漫画" required />
						<input type="text" name="work1[price]" class="line_third" maxlength="255" placeholder="30元" required />
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
				</div>
				<div id="other_info" class="info_div">
					<label class="normal_label" for="space_check">留言：</label>
					<textarea id="textarea" name="textarea" placeholder=""></textarea>
				</div>
				<div id="reg_info" class="info_div">
					<label class="normal_label" for="embed-captcha">验证码（GEETEST）：</label>
					<div id="embed-captcha"></div>
				    <p id="wait" class="show">正在加载验证码...</p>
				    <p id="notice" class="hide">请先完成验证</p>
				</div>
			</div>
			<input id="link" class="info gray" type="submit" formtarget="_self" value="" disabled />
		</form>
	</div>
    <script type="text/javascript">
		$('#link').attr("disabled", "disabled");
	
	    var handlerEmbed = function (captchaObj) {
	        $("#embed-submit").click(function (e) {
	            var validate = captchaObj.getValidate();
	            if (!validate) {
	                $("#notice")[0].className = "show";
	                setTimeout(function () {
	                    $("#notice")[0].className = "hide";
	                }, 2000);
	                e.preventDefault();
	            }
	        });
	        captchaObj.appendTo("#embed-captcha");
	        captchaObj.onReady(function () {
	            $("#wait")[0].className = "hide";
	        });
	        captchaObj.onSuccess(function () {
				$("#link").removeClass("gray");
				$('#link').removeAttr("disabled");
	        });
	        captchaObj.onError(function () {
				$("#link").addClass("gray");
	    		$('#link').attr("disabled", "disabled");
	        });
	    };
	    var data = <?=$lang["captcha"]?>;
        console.log(data);
        initGeetest({
            gt: data.gt,
            challenge: data.challenge,
            new_captcha: data.new_captcha,
            product: "popup",
            offline: !data.success
        }, handlerEmbed);
    </script>
</body>
</html>
