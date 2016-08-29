$(document).ready(function() {
	
	/**
	 * [if description]
	 * @param  {[type]} ($("#one").length >             0) [description]
	 * @return {[type]}                   [description]
	 */
	if (($("#datatable_default").length > 0)){
	    $('#datatable_default').DataTable();
	}

	if (($("#action_decript").length > 0)){
	    $('#action_decript').click(function(){
	    	$('#div_key_encryptedtext').show('fast');
	    });
	}

	if (($("#action_encript").length > 0)){
	    $('#action_encript').click(function(){
	    	$('#div_key_encryptedtext').hide('fast');
	    	$('#key').val('');
	    });
	}

	//If it is a 
	if (($("#prompt").length > 0)){

		//
		$('#command').focus();

		$('#command').keydown(function(e) {

		    if(e.which == 13) {
		        e.preventDefault();
		    	getCommandLine();
		    }
		    if(e.which == 38) {
		        e.preventDefault();
		    	getUpDownCommandLine('up');
		    }
		    if(e.which == 40) {
		        e.preventDefault();
		    	getUpDownCommandLine('down');
		    }
		});
	}
});

function getCommandLine(command)
{		
	$command = $('#command').val();
	$machine = $('#machine').val();
	$.post( "#",{module:'ssh_command', command: $command, machine: $machine}, 
		function( data ) {
			command = $('#command').val();
			user_ip = $('#user_ip_command_line').html();
			cifrao  = $('#cifrao_command_line').html();
			$('#last_commandline').append('<p>'+user_ip+' '+cifrao+' '+command+' '+data+'<p>');
			$('#command').val('');
			$('#command').focus();

		}
	);
}

function getUpDownCommandLine($direction)
{
	$position = $('#current_command_position').val();
	$.post( "#",{module:'ssh_command_updown', position: $position, direction: $direction}, 
		function( data ) {
			//altera posição atual
			if(data != ''){
				if($direction == 'up'){
					$position_atual = eval($('#current_command_position').val()) + 1 
					$('#current_command_position').val($position_atual);
				} else {
					$position_atual = eval($('#current_command_position').val()) - 1 
					$('#current_command_position').val($position_atual);
				}
			}
			$('#command').val(data);
			$('#command').focus();
		}
	);
}