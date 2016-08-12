//素材确定按钮回调函数
function newsOkButon(materialId,newsHtml) {
	var materialId = materialId || 0;
	var newsHtml = newsHtml || '';
	newsHtml.find('.tw_edit').remove();
	newsHtml.find('.twp').remove();

	//赋值操作 			
	$('#material_id').val(materialId);
	$('#change_imgtext').val('true');

	$('.ivrOfs_con').html(newsHtml).find('.TW_box').css({'margin': '0px', 'margin-top': '2px', 'width': '340px'});
	postTypeChangeHeight();
}

