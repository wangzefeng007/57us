$(function () {
	InicoHeight();
	//显示门店信息
	$(".GetCarDetailIns .ShowShop").click(function() {
		$(this).hide();
		$(".GetCarDetailInsLi").find("table").removeClass("hidden");
		InicoHeight();
	});
})

function InicoHeight() {
	var InicoHeight1 = $(".GetCarDetailInsLi").eq(0).offset().top;
	var InicoHeight2 = $(".GetCarDetailInsLi").eq(1).offset().top;
	$(".GetCarDetailIns .Inico").height(InicoHeight2 - InicoHeight1 + 20);
}