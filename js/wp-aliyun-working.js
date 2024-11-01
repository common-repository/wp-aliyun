$(document).ready(function(){
	if ($('#logfile').length>0) {
		var refreshId = setInterval(function() {
			$.ajax({
				type: 'POST',
				url: $('#wpaliyunworkingajaxurl').val(),
				cache: false,
				data: {
					logfile: $('#logfile').val(),
					logpos: $('#logpos').val()
				},
				dataType: 'json',
				success: function(rundata) {
					$info = rundata.LOG;
					if ('' != $info) {
						$('#showworking').append($info);
						$('#logpos').val(rundata.logpos);
						$('#showworking').scrollTop(rundata.logpos*-12);
					}					
				},
				error : function(XMLResponse) {
					/*$info =  'error：';
					$("#a").text($info);		
					$('#showworking').append($info);*/
				}
			});
		}, 1000);
	}
})