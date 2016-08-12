//查看图片
function LigImg() {
	this.showImg = function(obj, url) {
		var _this = $(obj);
		if (url == 'undefined' || url == null || url == " ") {
			url = obj.src;
		}
		this.Imgload(url);

		var ImgParent = _this.parent();
		ImgParent.css({
			'position' : 'relative'
		});
		var loadimgT = _this.height() / 2 + 2;
		var loadimgL = _this.width() / 2 + 2;
		var Loadimg = $(
				'<img style="position:absolute;width:16px;height:16px" src="./Public_1/images/Imgload.gif"/>')
				.css({
					'left' : '' + loadimgL + 'px',
					'top' : '' + loadimgT + 'px'
				}).appendTo(ImgParent)

		var imgobj = this;

		imgobj.img.onload = function() {

			Loadimg.remove();

			var Nimgw = imgobj.img.width;
			var Nimgh = imgobj.img.height;
			var Wz = document.documentElement.clientWidth
					|| document.body.clientWidth;
			var Hz = document.documentElement.clientHeight
					|| document.body.clientHeight;

			if (Nimgh > Hz - 30 && Nimgw < Wz - 30) {
				Nimgh = Hz - 30;
				Nimgw = (Hz - 30) / imgobj.img.height * Nimgw;
			} else if (Nimgw > Wz - 30) {
				Nimgw = Wz - 30;
				Nimgh = (Wz - 30) / imgobj.img.width * Nimgh;
			}

			var wc = Nimgw / 2;
			var hc = Nimgh / 2;
			var tccL = Wz / 2 - wc;
			var tccT = Hz / 2 - hc;

			// var _this = $(obj);
			var NewImg = $('<img src="' + imgobj.img.src + '"></img>')
					.appendTo('body');

			if (window.top.length == 0) {
				var offsetT = _this.offset().top;
				var offsetL = _this.offset().left;
			} else {
				var offsetL = tccL;
				var offsetT = -100;
			}

			NewImg.css({
				'position' : 'absolute',
				'z-index' : '4000',
				'top' : '' + offsetT + 'px',
				'left' : '' + offsetL + 'px',
				'width' : '' + _this.width() + 'px',
				'height' : '' + _this.height() + 'px'
			});

			var makObj = $(
					'<div style="position:fixed; display:block;left:0px; top:0px; width:100%; height: 100%; z-index:3000; "><div style="position:absolute;left:0px; top:0px; width:100%; height: 100%; opacity:0; display: block; background:#0E1011;" class="mak"></div></div>')
					.appendTo('body');
			makObj.find('.mak').animate({
				opacity : 0.6
			}, 'slow');

			// alert(Nimgw+'<< >>'+Nimgh);

			setTimeout(function() {
				NewImg.animate({
					left : '' + tccL + '',
					top : '' + tccT + '',
					width : '' + Nimgw + '',
					height : '' + Nimgh + ''
				}, 'slow');
			}, 250);

			makObj.click(function() {
				var _this = $(this);
				_this.animate({
					opacity : '0'
				}, 650);
				setTimeout(function() {
					_this.remove();
				}, 650);
				NewImg.remove();
			});

			$('.jsbox_close').live('click', function() {
				if (ligImg.img.onload) {
					ligImg.img.onload = false;
				}
			});
		}

	},
	// 图片加载判断
	this.Imgload = function(url) {
		this.img = new Image();
		this.img.src = url;
		this.img.onload = {};
	}
}