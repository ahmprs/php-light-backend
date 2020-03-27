function signOut() {
	$.post('./srv/api/sign-out', {}, (d, s) => {
		console.log(d);
		init();
	});
}

function init() {
	$.post('./srv/api/sign-in/state', {}, (d, s) => {
		try {
			// console.log(d);

			// if signed in
			if (d['ok'] == 1) {
				$('#btn_sign_out').removeClass('hide');
				$('#div_sign_in').addClass('hide');
				$('#div_sign_up').addClass('hide');
				$('#div_change_password').removeClass('hide');
			} else {
				$('#btn_sign_out').addClass('hide');
				$('#div_sign_in').removeClass('hide');
				$('#div_sign_up').removeClass('hide');
				$('#div_change_password').addClass('hide');
			}
		} catch (err) {
			console.log(err);
		}
	});
}

function signIn() {
	$.post('./srv/api/sign-in/new-seed', {}, function(d, s) {
		console.log({ d, s });
		try {
			if (d['ok'] == 1) {
				var seed = d['result'];
				var user_name = $('#txt_user_name_sign_in').val();
				var user_pass = $('#txt_user_pass_sign_in').val();
				var h = getMd5(user_pass);
				var otp = getMd5(h + seed);

				$.post('./srv/api/sign-in', { user_name, otp }, function(dd, ss) {
					try {
						console.log({ dd, ss });
						init();
					} catch (err) {
						console.log(err);
					}
				});
			}
		} catch (err) {
			console.log(err);
		}
	});
}

function signUp() {
	var user_name = $('#txt_user_name_sign_up').val();
	var user_pass = $('#txt_user_pass_sign_up').val();
	var user_pass_hash = getMd5(user_pass);
	$.post('./srv/api/sign-up', { user_name, user_pass_hash }, (d, s) => {
		console.log({ d, s });
		init();
	});
}

function changePassword() {
	var user_pass = $('#txt_user_pass_change').val();
	var user_pass_hash = getMd5(user_pass);

	$.post('./srv/api/change-password', { user_pass_hash }, function(d, s) {
		try {
			console.log({ d, s });
			init();
		} catch (err) {
			console.log(err);
		}
	});
}
